<?php

namespace App\EventListener;


use League\Bundle\OAuth2ServerBundle\Event\UserResolveEvent;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;
use Symfony\Component\Security\Core\User\UserProviderInterface;

#[AsEventListener]
class UserResolveListener
{
    public function __construct
    (
        private UserProviderInterface $userProvider
    )
    {}

    public function __invoke(UserResolveEvent $event): void{

        dd( $this->userProvider->loadUserByIdentifier($event->getUsername()));

    }
}