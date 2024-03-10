<?php

namespace App\Controller;

use App\Entity\Client;
use App\Entity\User;
use App\Repository\ClientRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use JMS\Serializer\SerializationContext;
use JMS\Serializer\SerializerInterface;
use Nelmio\ApiDocBundle\Annotation\Model;
use Psr\Cache\InvalidArgumentException;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Contracts\Cache\ItemInterface;
use Symfony\Contracts\Cache\TagAwareCacheInterface;
use OpenApi\Attributes as OA;

class UserController extends AbstractController
{
    /**
     * @param UserRepository $userRepository
     * @param Request $request
     * @param TagAwareCacheInterface $cachePool
     * @param SerializerInterface $serializer
     * @return JsonResponse
     * @throws InvalidArgumentException
     */
    #[Route('/api/users', name: 'users', methods: ['GET'])]
    #[OA\Response(
        response: 200,
        description: 'Retourne la liste des utilisateurs',
        content: new OA\JsonContent(
            type: 'array',
            items: new OA\Items(ref: new Model(type: User::class, groups: ['getUsers']))
        )
    )]
    #[OA\Parameter(
        name: 'page',
        description: "La page que l'on veut récupérer",
        in: 'query',
        schema: new OA\Schema(type: 'int')
    )]
    #[OA\Parameter(
        name: 'limit',
        description: "Le nombre d'éléments que l'on veut récupérer",
        in: 'query',
        schema: new OA\Schema(type: 'int')
    )]
    #[OA\Tag(name: 'Users')]
    public function getAllUsers(UserRepository         $userRepository,
                                Request                $request,
                                TagAwareCacheInterface $cachePool,
                                SerializerInterface    $serializer): JsonResponse
    {
        $page = $request->get('page', 1);
        $limit = $request->get('limit', 3);

        $idCache = 'getAllUsers' . $page . '-' . $limit;

        $userList = $cachePool->get($idCache,
            function (ItemInterface $item) use ($userRepository, $page, $limit) {
                $item->tag('UsersCache');
                $item->expiresAfter(300);
                return $userRepository->findUsersWithPagination($page, $limit);
            });

        $context = SerializationContext::create()->setGroups(['getUsers']);
        $jsonUserList = $serializer->serialize($userList, 'json', $context);

        return new JsonResponse($jsonUserList, Response::HTTP_OK, [], true);

    }

    #[Route('/api/users-client/{client}', name: 'users_client', methods: ['GET'])]
    public function getAllUsersByClient(UserRepository         $userRepository,
                                        Client                 $client,
                                        Request                $request,
                                        TagAwareCacheInterface $cachePool,
                                        SerializerInterface    $serializer): JsonResponse
    {
        $page = $request->get('page', 1);
        $limit = $request->get('limit', 3);

        $idCache = 'getAllUsers' . $page . '-' . $limit;

        $userList = $userRepository->findUsersByClientWithPagination($client, $page, $limit);

        $context = SerializationContext::create()->setGroups(['getUsers']);
        $jsonUserList = $serializer->serialize($userList, 'json', $context);

        return new JsonResponse($jsonUserList, Response::HTTP_OK, [], true);

    }

    /**
     * @param User $user
     * @param SerializerInterface $serializer
     * @return JsonResponse
     */
    #[Route('/api/users/{id}', name: 'detailUser', methods: ['GET'])]
    public function getDetailUser(User $user, SerializerInterface $serializer): JsonResponse
    {
        $context = SerializationContext::create()->setGroups(['getUsers']);
        $jsonUser = $serializer->serialize($user, 'json', $context);

        return new JsonResponse($jsonUser, Response::HTTP_OK, [], true);
    }


    /**
     * @param User $user
     * @param EntityManagerInterface $em
     * @return JsonResponse
     */
    #[Route('/api/users/{id}', name: 'deleteUser', methods: ['DELETE'])]
    #[Security("is_granted('ROLE_ADMIN')")]
    public function deleteUser(User $user, EntityManagerInterface $em): JsonResponse
    {

        $em->remove($user);
        $em->flush();

        return new JsonResponse(null, Response::HTTP_NO_CONTENT);
    }

    #[Route('/api/users', name: 'createUser', methods: ['POST'])]
    #[Security("is_granted('ROLE_ADMIN')")]
    public function createUser(Request                     $request,
                               SerializerInterface         $serializer,
                               EntityManagerInterface      $em,
                               UrlGeneratorInterface       $urlGenerator,
                               ClientRepository            $clientRepository,
                               ValidatorInterface          $validator,
                               UserPasswordHasherInterface $passwordHasher): JsonResponse
    {

        $user = $serializer->deserialize($request->getContent(), User::class, 'json');

        $errors = $validator->validate($user);
        if ($errors->count() > 0) {
            return new JsonResponse($serializer->serialize($errors, 'json'),
                Response::HTTP_BAD_REQUEST, [], true);
        }

        $content = $request->toArray();
        $idClient = $content["idClient"];
        $client = $clientRepository->find($idClient);

        if (!$client) {
            $errorMessage = ['error' => 'Client does not exist'];
            return new JsonResponse($serializer->serialize($errorMessage, 'json'),
                Response::HTTP_NOT_FOUND, [], true);
        }

        $user->setClient($client);
        $user->setRoles(['ROLE_USER']);
        $plainPassword = $content['password'];
        $user->setPassword($passwordHasher->hashPassword($user, $plainPassword));

        $em->persist($user);
        $em->flush();

        $context = SerializationContext::create()->setGroups(['getUsers']);
        $jsonUser = $serializer->serialize($user, 'json', $context);
        $location = $urlGenerator->generate('detailUser', ['id' => $user->getId()],
            UrlGeneratorInterface::ABSOLUTE_URL);

        return new JsonResponse($jsonUser, Response::HTTP_CREATED, ['location' => $location], true);
    }

}
