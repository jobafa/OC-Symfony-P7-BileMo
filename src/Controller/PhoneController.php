<?php

namespace App\Controller;

use App\Entity\Phone;
use OpenApi\Annotations as OA;
use App\Repository\PhoneRepository;
use JMS\Serializer\SerializerInterface;
use JMS\Serializer\SerializationContext;
use Nelmio\ApiDocBundle\Annotation\Model;
use Symfony\Contracts\Cache\ItemInterface;
use Nelmio\ApiDocBundle\Annotation\Security;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Contracts\Cache\TagAwareCacheInterface;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class PhoneController extends AbstractController
{
    /**
     * GETS THE LIST OF BILEMO PHONES
     * 
     * @Route("/api/phones", name="app_phone", methods={"GET"})
     * 
     * @OA\Response(
     *     response=200,
     *     description="phones list",
     *     @OA\JsonContent(
     *        type="array",
     *        @OA\Items(ref=@Model(type=Phone::class, groups={"get:phones"}))
     *     )
     * )
     * @OA\Response(
     *     response=400,
     *     description="Bad Request: Method not allowed !",
     *     @OA\JsonContent(
     *        type="array",
     *        @OA\Items(ref=@Model(type=Phone::class, groups={"get:phones"}))
     *     )
     * )
     * @OA\Response(
     *     response=401,
     *     description="Expired Token !",
     *     @OA\JsonContent(
     *        type="array",
     *        @OA\Items(ref=@Model(type=Phone::class, groups={"get:phones"}))
     *     )
     * )
     * @OA\Parameter(
     *     name="page",
     *     in="query",
     *     description="the page to display",
     *     @OA\Schema(type="int")
     * )
     *
     * @OA\Parameter(
     *     name="limit",
     *     in="query",
     *     description="the number of users to display",
     *     @OA\Schema(type="int")
     * )
     * @OA\Tag(name="Phones")
     *
     * @param PhoneRepository $phoneRepository
     * @param SerializerInterface $serializer
     * @param Request $request
     * @return JsonResponse
     *
     */
    public function getphoneList(PhoneRepository $phoneRepository, SerializerInterface $serializer, Request $request, TagAwareCacheInterface $cache): JsonResponse
    {
        $page = $request->get('page', 1);
        $limit = $request->get('limit', 3);
        
        //MISE EN CACHE après avoir converti les données en JSON
        $idCache = "getAllPhones-" . $page . "-" . $limit;
        
        $jsonPhoneList = $cache->get($idCache, function (ItemInterface $item) use ($phoneRepository, $page, $limit, $serializer) {
            $item->tag("phonesCache");
            
            $phoneList = $phoneRepository->findAllWithPagination($page, $limit);

            $context = SerializationContext::create();
            return $serializer->serialize($phoneList, 'json', $context);

        });

        return new JsonResponse($jsonPhoneList, Response::HTTP_OK, [], true);

        
    }

    /**
     * GETS THE PHONE'S DETAILS
     * 
     * @Route("/api/phones/{id}", name="detailPhone", methods={"GET"})
     * 
     * @OA\Response(
     *     response=200,
     *     description="Phone's Details",
     *     @OA\JsonContent(
     *        type="array",
     *        @OA\Items(ref=@Model(type=Phone::class, groups={"get:phones"}))
     *     )
     * )
     * @OA\Response(
     *     response=400,
     *     description="Bad Request: Method not allowed !",
     *     @OA\JsonContent(
     *        type="array",
     *        @OA\Items(ref=@Model(type=Phone::class, groups={"get:phones"}))
     *     )
     * )
     * @OA\Response(
     *     response=401,
     *     description="Expired Token !",
     *     @OA\JsonContent(
     *        type="array",
     *        @OA\Items(ref=@Model(type=Phone::class, groups={"get:phones"}))
     *     )
     * )
     * @OA\Tag(name="Phones")
     *
     * @param Phone $phone
     * @param Request $request
     * @param SerializerInterface $serializer
     * @return JsonResponse
     *
     */
    public function getDetailPhone(Phone $phone, Request $request, SerializerInterface $serializer, ValidatorInterface $validator): JsonResponse
    {
        // On valide
        $validator->validate($request);

        if (!ctype_digit($request->get('id'))) {
            
            throw new HttpException( 400,  'Bad Request !'); 
            
        }
            $context = SerializationContext::create();
            $jsonPhone = $serializer->serialize($phone, 'json', $context);  /**/

            return new JsonResponse($jsonPhone, Response::HTTP_OK, [], true);
    }
    
    
}
