<?php

namespace App\Controller;

use App\Entity\User;
use App\Service\MailerService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpKernel\Attribute\AsController;


#[AsController]
class EmailController  extends AbstractController
{

    public function __construct
    (
        private MailerService $mailerService

    )
    {
    }


    public function __invoke(User $user): User{

        $email = [
            'from'=>'test@test.com',
            'to'=>'test2@test.com',
            'theme'=>'A very important question',
            'question'=>'How to poop?'
        ];

        $this->mailerService->sendMail($email);

        return $user;
    }

}