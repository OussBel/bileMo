<?php

namespace App\Service;

use App\Entity\Client;
use App\Entity\User;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

readonly class UserService
{
    /**
     * @param Security $security
     */
    public function __construct(private Security $security)
    {
    }


    /**
     * @return JsonResponse|Client
     */
    public function validateLoggedInClient(): JsonResponse|Client
    {
        $loggedInClient = $this->security->getUser();

        if (!$loggedInClient instanceof Client) {
            return new JsonResponse(['error' => 'Unauthorized'], Response::HTTP_UNAUTHORIZED);
        }

        return $loggedInClient;
    }

    /**
     * @param User $user
     * @return JsonResponse|null
     */
    public function validateClientAccess(User $user): ?JsonResponse
    {
        $loggedInClient = $this->security->getUser();

        if ($user->getClient() !== $loggedInClient) {
            return new JsonResponse(['error' => 'Unauthorized'], Response::HTTP_UNAUTHORIZED);
        }

        return null;
    }

}