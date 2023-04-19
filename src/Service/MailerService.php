<?php

namespace App\Service;

use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Address;
use Symfony\Component\Mime\Email;

class MailerService
{
    public function __construct
    (
        private MailerInterface $mailer
    ){

    }

    public function sendMail(array $dataEmail){

        $email = (new TemplatedEmail())
            ->from($dataEmail['from'])
            ->to(new Address($dataEmail['to']))
            ->subject($dataEmail['theme'])
            ->htmlTemplate('email/question.html.twig')
            ->context([
                'question' => $dataEmail['question'],
            ]);

        $this->mailer->send($email);
    }
}