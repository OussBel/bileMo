<?php

namespace App\Controller;

use App\Entity\User;
use App\Repository\ClientRepository;
use App\Repository\UserRepository;
use App\Service\UserService;
use Doctrine\ORM\EntityManagerInterface;
use JMS\Serializer\SerializationContext;
use JMS\Serializer\SerializerInterface;
use Nelmio\ApiDocBundle\Annotation\Model;
use Psr\Cache\InvalidArgumentException;
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
     * @param UserService $userService
     */
    public function __construct(private readonly UserService $userService)
    {
    }

    /**
     * @param UserRepository $userRepository
     * @param SerializerInterface $serializer
     * @param Request $request
     * @param TagAwareCacheInterface $cachePool
     * @return JsonResponse
     * @throws InvalidArgumentException
     */
    #[Route('/api/users', name: 'users', methods: ['GET'])]
    #[OA\Response(
        response: 200,
        description: 'Retourne la liste des livres',
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
                                SerializerInterface    $serializer,
                                Request                $request,
                                TagAwareCacheInterface $cachePool): JsonResponse
    {
        $loggedInClient = $this->userService->validateLoggedInClient();

        $page = $request->get('page', 1);
        $limit = $request->get('limit', 3);

        $idCache = "getAllUsers" . $page . "-" . $limit;

        $userList = $cachePool->get($idCache,
            function (ItemInterface $item) use ($userRepository, $page, $limit, $loggedInClient) {
                $item->tag('UsersCache');
                $item->expiresAfter(300);
                return $userRepository->findUsersByClient($loggedInClient, $page, $limit);
            });
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
        $validationResult = $this->userService->validateClientAccess($user);

        if ($validationResult instanceof JsonResponse) {
            return $validationResult;
        }

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
    public function deleteUser(User $user, EntityManagerInterface $em): JsonResponse
    {
        $validationResult = $this->userService->validateClientAccess($user);

        if ($validationResult instanceof JsonResponse) {
            return $validationResult;
        }

        $em->remove($user);
        $em->flush();

        return new JsonResponse(null, Response::HTTP_NO_CONTENT);
    }

    /**
     * @param Request $request
     * @param SerializerInterface $serializer
     * @param EntityManagerInterface $em
     * @param ClientRepository $clientRepository
     * @param UrlGeneratorInterface $urlGenerator
     * @param ValidatorInterface $validator
     * @param UserPasswordHasherInterface $passwordHasher
     * @return JsonResponse
     */
    #[Route('/api/users', name: 'createUser', methods: ['POST'])]
    public function createUser(Request                     $request,
                               SerializerInterface         $serializer,
                               EntityManagerInterface      $em,
                               ClientRepository            $clientRepository,
                               UrlGeneratorInterface       $urlGenerator,
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
        $idClient = $content["idClient"] ?? -1;
        $user->setClient($clientRepository->find($idClient));
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
