<?php

namespace App\Controller;

use App\Entity\Mobile;
use App\Repository\MobileRepository;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Cache\InvalidArgumentException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Contracts\Cache\ItemInterface;
use Symfony\Contracts\Cache\TagAwareCacheInterface;

class MobileController extends AbstractController
{

    /**
     * @param MobileRepository $mobileRepository
     * @param SerializerInterface $serializer
     * @param Request $request
     * @param TagAwareCacheInterface $cachePool
     * @return JsonResponse
     * @throws InvalidArgumentException
     */
    #[Route('/api/mobiles', name: 'mobiles', methods: ['GET'])]
    public function getAllMobiles(MobileRepository       $mobileRepository,
                                  SerializerInterface    $serializer,
                                  Request                $request,
                                  TagAwareCacheInterface $cachePool
    ): JsonResponse
    {
        $page = $request->get('page', 1);
        $limit = $request->get('limit', 3);

        $idCache = "getAllMobiles" . $page . '-' . $limit;

        $mobileList = $cachePool->get($idCache,
            function (ItemInterface $item) use ($mobileRepository, $page, $limit) {
                echo("L'élément n'est pas encore en cache");
                $item->tag('MobilesCache');
                $item->expiresAfter(300);
                return $mobileRepository->findAllWithPagination($page, $limit);
            });

        $mobileList = $mobileRepository->findAllWithPagination($page, $limit);
        $jsonMobileList = $serializer->serialize($mobileList, 'json');
        return new JsonResponse($jsonMobileList, Response::HTTP_OK, [], true);
    }

    /**
     * @param Mobile $mobile
     * @param SerializerInterface $serializer
     * @return JsonResponse
     */
    #[Route('/api/mobiles/{id}', name: 'detailMobile', methods: ['GET'])]
    public function getDetailMobile(Mobile $mobile, SerializerInterface $serializer): JsonResponse
    {
        $jsonMobile = $serializer->serialize($mobile, 'json');
        return new JsonResponse($jsonMobile, Response::HTTP_OK, [], true);
    }

}
