<?php

namespace App\Controller;

use App\Entity\Phone;
use App\Repository\PhoneRepository;
use JMS\Serializer\SerializerInterface;
use Nelmio\ApiDocBundle\Annotation\Model;
use Psr\Cache\InvalidArgumentException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Contracts\Cache\ItemInterface;
use Symfony\Contracts\Cache\TagAwareCacheInterface;
use OpenApi\Attributes as OA;


class PhoneController extends AbstractController
{

    /**
     * @throws InvalidArgumentException
     */
    #[Route('/api/phones', name: 'phones', methods: ['GET'])]

    #[OA\Response(
        response: 200,
        description: 'Retourne la liste des téléphones',
        content: new OA\JsonContent(
            type: 'array',
            items: new OA\Items(ref: new Model(type: Phone::class))
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
    #[OA\Tag(name: 'Phones')]
    public function getAllPhones(PhoneRepository $phoneRepository, SerializerInterface $serializer,
    Request $request,TagAwareCacheInterface $cachePool ): JsonResponse
    {

        $page = $request->get('page', 1);
        $limit = $request->get('limit', 3);

        $idCache = "getAllPhones" . $page . "-" . $limit;

        $phoneList = $cachePool->get($idCache,
            function (ItemInterface $item) use ($phoneRepository, $page, $limit) {
                $item->tag('PhonesCache');
                $item->expiresAfter(300);
                return $phoneRepository->findAllWithPagination( $page, $limit);
            });
        $phoneList = $phoneRepository->findAllWithPagination($page, $limit);
        $jsonPhoneList = $serializer->serialize($phoneList, 'json');

        return new JsonResponse($jsonPhoneList, Response::HTTP_OK, [], true);
    }

    #[Route('/api/phones/{id}', name: 'detailPhone', methods: ['GET'])]
    public function getDetailMobile(Phone $phone, SerializerInterface $serializer): JsonResponse
    {
        $jsonPhone = $serializer->serialize($phone, 'json');
        return new JsonResponse($jsonPhone, Response::HTTP_OK, [], true);
    }
}
