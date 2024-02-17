<?php

namespace App\Controller;

use App\Entity\Client;
use App\Repository\ClientRepository;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Serializer\SerializerInterface;

class UserController extends AbstractController
{
    /**
     * @param UserRepository $userRepository
     * @param SerializerInterface $serializer
     * @param int $clientId
     * @param ClientRepository $clientRepository
     * @return JsonResponse
     */
    #[Route('/api/users/{clientId}', name: 'users', methods: ['GET'])]
    public function getUsersByClient(UserRepository $userRepository, SerializerInterface $serializer,
                                      int $clientId, ClientRepository $clientRepository): JsonResponse
    {
        $client = $clientRepository->find($clientId);
        $userList = $userRepository->findAllUsersByClient($client);
        $jsonUserList = $serializer->serialize($userList, 'json',['groups' => 'getUsers']);

        return new JsonResponse($jsonUserList, Response::HTTP_OK, [], true);
    }

}
