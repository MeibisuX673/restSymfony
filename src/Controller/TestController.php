<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;


class TestController extends AbstractController
{
    #[Route('/test', name: 'test')]
    #[IsGranted('IS_AUTHENTICATED_REMEMBERED')]
    public function __invoke(AuthenticationUtils $authenticationUtils): Response{

        return new JsonResponse(['id'=>'2']);
    }
}