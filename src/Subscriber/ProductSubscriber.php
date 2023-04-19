<?php

namespace App\Subscriber;

use ApiPlatform\Symfony\EventListener\EventPriorities;
use App\Entity\Product;
use App\Repository\ProductRepository;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Event\ViewEvent;
use Symfony\Component\HttpKernel\KernelEvents;


class ProductSubscriber implements EventSubscriberInterface
{

    private ProductRepository $productRepository;

    public function __construct
    (
        ProductRepository $productRepository
    )
    {
        $this->productRepository = $productRepository;
    }

    public static function getSubscribedEvents(): array
    {
        return [
            KernelEvents::VIEW => ['incrementTimesViewed', EventPriorities::PRE_SERIALIZE]
        ];
    }

    public function incrementTimesViewed(ViewEvent $event){

        $product = $event->getControllerResult();
        $method = $event->getRequest()->getMethod();

        if (!$product instanceof Product || Request::METHOD_GET !== $method) {
            return;
        }

        $product = $this->productRepository->incrementTimesViewed($product->getId());
        $event->setControllerResult($product);
    }
}