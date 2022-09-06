<?php

namespace App\EventSubscriber;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class ExceptionSubscriber implements EventSubscriberInterface
{
    public function onKernelException(ExceptionEvent $event): void
    {
        $exception = $event->getThrowable();
        
        //

        if ($exception instanceof HttpException) {
            $status = $exception->getStatusCode();
            //dd($status);
            if($status === 403){
                $message = "Accès Refusé : Vous n'avez pas les droits pour effectuer cette tache !";
            }
            elseif($status === 409){
                $message = "Cette adresse email existe dèjà !";
            }
            elseif($status === 404){
                $message = "Ressource Introuvable !";
            }elseif($status === 405){
                $message = "Methode non authorisé sur cette route !";
            }else{
                $message = $exception->getMessage();
            }
            $data = [
                'status' => $status,
                'message' => $message
                /* 'status' => $exception->getStatusCode(),
                'message' => $exception->getMessage() */
            ];
        

            $event->setResponse(new JsonResponse($data));
      }/*  else {
            $data = [
                'status' => 500, // Le status n'existe pas car ce n'est pas une exception HTTP, donc on met 500 par défaut.
                'message' => $exception->getMessage()
            ];

            $event->setResponse(new JsonResponse($data));
      } */
    }

    public static function getSubscribedEvents(): array
    {
        return [
            'kernel.exception' => 'onKernelException',
        ];
    }
}
