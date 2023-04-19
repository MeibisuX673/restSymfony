<?php

namespace App\Controller;

use App\Service\MailerService;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class EmailController  extends AbstractController
{

    public function __construct
    (
        private MailerService $mailerService

    )
    {
    }

    #[Route(path: '/email',name: 'email_send')]
    public function sendEmail(): Response{

        // dd($this->security->getUser());
        $email = [
            'from'=>'test@test.com',
            'to'=>'test2@test.com',
            'theme'=>'A very important question',
            'question'=>'How to poop?'
        ];

        $this->mailerService->sendMail($email);

        return new jsonResponse([
            'message'=>'send'
        ]);
    }

}