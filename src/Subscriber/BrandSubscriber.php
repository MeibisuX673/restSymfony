<?php

namespace App\Subscriber;

use ApiPlatform\Symfony\EventListener\EventPriorities;
use App\Entity\Brand;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\Event\ViewEvent;
use Symfony\Component\HttpKernel\KernelEvents;

class BrandSubscriber implements EventSubscriberInterface
{

    public static function getSubscribedEvents(): array
    {
        return [
//          KernelEvents::VIEW=>['addUser', EventPriorities::POST_VALIDATE]
        ];
        
    }
    
    public function addUser(ViewEvent $event){

        dd($event->getRequest()->getUser());
        $brand = $event->getControllerResult();

        if($brand instanceof Brand && $event->getRequest()->isMethod(Request::METHOD_POST)){
            $brand->user = $event->getRequest()->getUser();
        }
        dd($event->getControllerResult(),$event->getRequest());
    }
}