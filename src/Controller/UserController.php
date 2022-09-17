<?php

namespace App\Controller;

use App\Entity\User;
use App\Entity\Client;
use App\Repository\UserRepository;
use App\Service\VersioningService;
use App\Repository\ClientRepository;
use JMS\Serializer\SerializerInterface;
use Doctrine\ORM\EntityManagerInterface;
use JMS\Serializer\SerializationContext;
use Symfony\Contracts\Cache\ItemInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Contracts\Cache\TagAwareCacheInterface;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Nelmio\ApiDocBundle\Annotation\Model;
use Nelmio\ApiDocBundle\Annotation\Security;
use OpenApi\Annotations as OA;


class UserController extends AbstractController
{

     /**
     * GETS RELATED USERS LIST WITH PAGINATION
     *
     * * @Route("/api/users", name="app_clientusers", methods={"GET"})
     * 
     * @OA\Response(
     *     response=200,
     *     description="returns users list",
     *     @OA\JsonContent(
     *        type="array",
     *        @OA\Items(ref=@Model(type=User::class, groups={"get:users"}))
     *     )
     * )
     * @OA\Response(
     *     response=400,
     *     description="Bad Request: Method not allowed !",
     *     @OA\JsonContent(
     *        type="array",
     *        @OA\Items(ref=@Model(type=User::class, groups={"get:users"}))
     *     )
     * )
     * @OA\Response(
     *     response=401,
     *     description="Expired Token !",
     *     @OA\JsonContent(
     *        type="array",
     *        @OA\Items(ref=@Model(type=User::class, groups={"get:users"}))
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
     * @OA\Tag(name="Users")
     *
     * @param UserRepository $bookRepository
     * @param SerializerInterface $serializer
     * @param Request $request
     * @return JsonResponse
     *
     */
    public function getUsersByClient( UserRepository $userRepository, SerializerInterface $serializer, Request $request, TagAwareCacheInterface $cache): JsonResponse
    {
        $clientId = $this->getUser()->getId();
        $page = $request->get('page', 1);
        $limit = $request->get('limit', 3);
       
        //MISE EN CACHE après avoir converti les données en JSON
        $idCache = "getUsersByClient-" . $page . "-" . $limit;
        
        $jsonUsersList = $cache->get($idCache, function (ItemInterface $item) use ($clientId, $userRepository, $page, $limit, $serializer) {
            $item->tag("getUsersByClientCache");
            
            $usersList = $userRepository->findAllByClient($page, $limit, $clientId);
            
            $context = SerializationContext::create()->setGroups("get:users");
            
            return $serializer->serialize($usersList, 'json', $context);
        });

        return new JsonResponse($jsonUsersList, Response::HTTP_OK, [], true);

       
    }

    /**
     * GETS USER'S DETAILS WITH THE GIVEN ID
     * 
     * @Route("api/users/{id}", name="detailUser", methods={"GET"})
     * 
     * @OA\Parameter(
     *     name="id",
     *     in="path",
     *     required=true,
     *     description="user's Id",
     *     @OA\Schema(type="integer")
     * )
     * @OA\Response(
     *     response=200,
     *     description="returns a user's record ",
     *     @OA\JsonContent(
     *        type="array",
     *        @OA\Items(ref=@Model(type=User::class, groups={"get:users"}))
     *     )
     * )
     * @OA\Response(
     *     response=400,
     *     description="Bad Request: Method not allowed !",
     *     @OA\JsonContent(
     *        type="array",
     *        @OA\Items(ref=@Model(type=User::class, groups={"get:users"}))
     *     )
     * )
     * @OA\Response(
     *     response=401,
     *     description="Expired Token !",
     *     @OA\JsonContent(
     *        type="array",
     *        @OA\Items(ref=@Model(type=User::class, groups={"get:users"}))
     *     )
     * )
     * @OA\Tag(name="Users")
     *
     * @param User $user
     * @param UserRepository $bookRepository
     * @param SerializerInterface $serializer
     * @param Request $request
     * @return JsonResponse
     *
     */
    public function getUserByClient(User $user,UserRepository $userRepository, SerializerInterface $serializer, Request $request, TagAwareCacheInterface $cache, VersioningService $versioningService): JsonResponse
    {
        $idCache = "getUserByClient";
        
        $jsonUser = $cache->get($idCache, function (ItemInterface $item) use ($versioningService, $user, $request, $userRepository, $serializer) {

        $item->tag("getUserByClientCache");

        $clientId = $this->getUser()->getId();
        $userClientId = $user->getClient()->getId();
        
        if ($userClientId === $clientId) {
            $userdetail = $userRepository->findOneByClient($clientId, $request->get('id'));
            
        } else {
            throw new AccessDeniedHttpException();
        }
        
        $version = $versioningService->getVersion();
        $context = SerializationContext::create()->setGroups("get:users");
        $context->setVersion($version);
       
        return $serializer->serialize($userdetail, 'json', $context);
        });

        return new JsonResponse($jsonUser, Response::HTTP_OK, [], true);
    }

    /**
     * ADDS A  USER TO THE CLIENT LIST
     * 
    * @Route("/api/users", name="createUser", methods={"POST"})
    * @IsGranted("ROLE_ADMIN")
    *
       * @OA\Response(
     *     response=201,
     *     description="Creates a user related to the client ",
     *     @OA\JsonContent(
     *        type="array",
     *        @OA\Items(ref=@Model(type=User::class, groups={"get:users"}))
     *     )
     * )
     * @OA\Response(
     *     response=400,
     *     description="Bad Request: Method not allowed !",
     *     @OA\JsonContent(
     *        type="array",
     *        @OA\Items(ref=@Model(type=User::class, groups={"get:users"}))
     *     )
     * )
     * @OA\Response(
     *     response=401,
     *     description="Expired Token !",
     *     @OA\JsonContent(
     *        type="array",
     *        @OA\Items(ref=@Model(type=User::class, groups={"get:users"}))
     *     )
     * )
     * @OA\Response(
     *     response=409,
     *     description="This email  already exists !",
     *     @OA\JsonContent(
     *        type="array",
     *        @OA\Items(ref=@Model(type=User::class, groups={"get:users"}))
     *     )
     * )
     * @OA\Tag(name="Users")
     *
     * @param UserRepository $bookRepository
     * @param SerializerInterface $serializer
     * @param Request $request
     * @return JsonResponse
     *
     */
    public function addUserByClient(Request $request, SerializerInterface $serializer, EntityManagerInterface $em, UrlGeneratorInterface $urlGenerator, UserRepository $userRepository, ValidatorInterface $validator): JsonResponse 
    {
        $user = $serializer->deserialize($request->getContent(), User::class, 'json');

        // On vérifie les erreurs
        $errors = $validator->validate($user);

        if ($errors->count() > 0) {
            return new JsonResponse($serializer->serialize($errors, 'json'), JsonResponse::HTTP_BAD_REQUEST, [], true);
        }
        
        $emailExists = $userRepository->findOneBy(['email' => $user->getEmail()]);
        //TEST UNICITY OF EMAIL FIELD
        if ($emailExists) {
            throw new HttpException( 409,  'This email  already exists !');
            
        }
        $user->setClient($this->getUser());
        $user->setCreatedAt(new \DateTime('now'));

        $em->persist($user);
        $em->flush();
 
        $context = SerializationContext::create()->setGroups("get:users");
        $jsonUser =  $serializer->serialize($user, 'json', $context);
        
        $location = $urlGenerator->generate('detailUser', ['id' => $user->getId()], UrlGeneratorInterface::ABSOLUTE_URL);

        return new JsonResponse($jsonUser, Response::HTTP_CREATED, ["Location" => $location], true);
   }

    /**
    * DELETES THE USER WITH THE GIVEN ID

    * @Route("/api/users/{id}", name="deleteUser", methods={"DELETE"})
    * @IsGranted("ROLE_ADMIN")
    *
    * @OA\Parameter(
     *     name="id",
     *     in="path",
     *     required=true,
     *     description="User's Id",
     *     @OA\Schema(type="integer")
     * )
     * @OA\Response(
     *     response=204,
     *     description="No content:  User deleted."
     * )
     * @OA\Response(
     *     response=401,
     *     description="Expired JWT Token"
     * )
     * @OA\Response(
     *     response=403,
     *     description="Not allowed !"
     * )
     * @OA\Response(
     *     response=404,
     *     description="Not found."
     * )
     * @OA\Tag(name="users")
     * 
     * @param User $user
     * @param SerializerInterface $serializer
     * @param Request $request
     * @return JsonResponse
     * 
    */
    public function deleteUser(User $user, EntityManagerInterface $em, TagAwareCacheInterface $cachePool): JsonResponse 
    {
        $clientId = $this->getUser()->getId();
        $userClientId = $user->getClient()->getId();
       
        if ($userClientId !== $clientId) {
            throw new AccessDeniedHttpException();
        } 
        
        //INVALIDER LE TAG LIE A LA MISE EN CACHE DES USERS
        $cachePool->invalidateTags(["getUsersByClient"]);

        $em->remove($user);
        $em->flush();

        return new JsonResponse(null, Response::HTTP_NO_CONTENT);
    }

}
