<?php

namespace App\Controller;

//use Client;
use App\Entity\Client;
use App\Repository\ClientRepository;
use JMS\Serializer\SerializerInterface;
//use App\Repository\UserRepository;
use JMS\Serializer\SerializationContext;
use Symfony\Contracts\Cache\ItemInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Contracts\Cache\TagAwareCacheInterface;
//use Symfony\Component\Serializer\SerializerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

    /**
     * Require ROLE_SUPER_ADMIN
     * 
     * 
     */
class ClientController extends AbstractController
{

    /**
     * @Route("/api/clients", name="app_clients", methods={"GET"})
     */
    public function getClientsList(ClientRepository $clientRepository, SerializerInterface $serializer, Request $request, TagAwareCacheInterface $cache): JsonResponse
    {
        
        $page = $request->get('page', 1);
        $limit = $request->get('limit', 4);
        
        //MISE EN CACHE après avoir converti les données en JSON
        $idCache = "getAllClients-" . $page . "-" . $limit;
        
        $jsonClientList = $cache->get($idCache, function (ItemInterface $item) use ($clientRepository, $page, $limit, $serializer) {
            $item->tag("clientsCache");
            
            $clientList = $clientRepository->findAllWithPagination($page, $limit);

            //$context = SerializationContext::create()->setGroups(["get:clients"]);
             /*$context = new SerializationContext();
            $context->setGroups('get:users');*/
            //dd($context,$phoneList);
            //return $serializer->serialize($phoneList, 'json', $context);  
            $context = SerializationContext::create()->setGroups(['get:clients', 'get:users']);
            return $serializer->serialize($clientList, 'json', $context);  
            //return $serializer->serialize($clientList, 'json', ['groups' => array('get:clients', 'get:users')]);
        });

        return new JsonResponse($jsonClientList, Response::HTTP_OK, [], true);

    }

    /**
     * @Route("/api/clients/{id}", name="detailclient", methods={"GET"})
     */
    public function getDetailClient(Client $client, SerializerInterface $serializer): JsonResponse {
        //public function getDetailBook(int $id, SerializerInterface $serializer, BookRepository $bookRepository): JsonResponse {
    
            /* $book = $bookRepository->find($id);
            if ($book) {
                $jsonBook = $serializer->serialize($book, 'json');
                return new JsonResponse($jsonBook, Response::HTTP_OK, [], true);
            }
            return new JsonResponse(null, Response::HTTP_NOT_FOUND); */
           $context = SerializationContext::create()->setGroups(['get:clients', 'get:users']);
            $jsonClient = $serializer->serialize($client, 'json', $context);  /**/
            //$jsonClient = $serializer->serialize($client, 'json', ['groups' => array('get:clients', 'get:users')]);
            return new JsonResponse($jsonClient, Response::HTTP_OK, [], true);
       }
    
}
