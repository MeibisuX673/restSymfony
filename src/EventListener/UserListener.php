<?php

namespace App\EventListener;

use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Attribute\AsEntityListener;
use Doctrine\ORM\Event\PrePersistEventArgs;
use Doctrine\ORM\Events;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

#[AsEntityListener(event: Events::prePersist, method: 'prePersist',entity: User::class)]
class UserListener
{

    public function __construct(private UserPasswordHasherInterface $passwordHasher){

    }

    public function  prePersist(User $user, PrePersistEventArgs $eventArgs): void{

        $user->setPassword($this->passwordHasher->hashPassword($user,$user->getPassword()));
    }
}