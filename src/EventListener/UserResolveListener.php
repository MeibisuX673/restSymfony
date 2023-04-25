<?php

namespace App\EventListener;



use League\Bundle\OAuth2ServerBundle\Event\UserResolveEvent;
use League\Bundle\OAuth2ServerBundle\OAuth2Events;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;

#[AsEventListener(event: OAuth2Events::USER_RESOLVE)]
class UserResolveListener
{
    public function __construct
    (
        private UserProviderInterface $userProvider,
        private UserPasswordHasherInterface $passwordHasher
    )
    {}

    public function __invoke(UserResolveEvent $event){
        $user = $this->userProvider->loadUserByIdentifier($event->getUsername());

        if(!$this->passwordHasher->isPasswordValid($user,$event->getPassword())){
            return;
        }
        $event = $event->setUser($user);
        return $event;

    }
}