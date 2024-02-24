<?php

namespace App\Controller;

use App\Entity\User;
use App\Repository\ClientRepository;
use App\Repository\UserRepository;
use App\Service\UserService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

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
     * @return JsonResponse
     */
    #[Route('/api/users', name: 'users', methods: ['GET'])]
    public function getAllUsers(UserRepository $userRepository, SerializerInterface $serializer,
                                Request        $request): JsonResponse
    {
        $loggedInClient = $this->userService->validateLoggedInClient();

        $page = $request->get('page',1);
        $limit = $request->get('limit', 3);
        $userList = $userRepository->findUsersByClient($loggedInClient, $page, $limit);
        $jsonUserList = $serializer->serialize($userList, 'json', ['groups' => 'getUsers']);

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

        $jsonUser = $serializer->serialize($user, 'json', ['groups' => 'getUsers']);

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
     * @return JsonResponse
     */
    #[Route('/api/users', name: 'createUser', methods: ['POST'])]
    public function createUser(Request                $request, SerializerInterface $serializer,
                               EntityManagerInterface $em,
                               ClientRepository       $clientRepository,
                               UrlGeneratorInterface  $urlGenerator,
                               ValidatorInterface     $validator): JsonResponse
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

        $em->persist($user);
        $em->flush();

        $jsonUser = $serializer->serialize($user, 'json', ['groups' => 'getUsers']);
        $location = $urlGenerator->generate('detailUser', ['id' => $user->getId()],
            UrlGeneratorInterface::ABSOLUTE_URL);

        return new JsonResponse($jsonUser, Response::HTTP_CREATED, ['location' => $location], true);
    }

}
