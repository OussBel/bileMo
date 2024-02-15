<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;

class MobileController extends AbstractController
{
    #[Route('/api/mobiles', name: 'mobile')]
    public function getAllMobiles(): JsonResponse
    {
        return new JsonResponse([

        ]);
    }
}
