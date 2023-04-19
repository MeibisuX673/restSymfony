<?php

namespace App\Security;


use App\Repository\UserSecurityRepository;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Http\Authenticator\AbstractLoginFormAuthenticator;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\CsrfTokenBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\RememberMeBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\UserBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Credentials\PasswordCredentials;
use Symfony\Component\Security\Http\Authenticator\Passport\Passport;
use Symfony\Component\Security\Http\Util\TargetPathTrait;


class LoginFormAuthenticator extends AbstractLoginFormAuthenticator
{

    use TargetPathTrait;

    public function __construct
    (
        UserSecurityRepository $userSecurityRepository,
        RouterInterface $router
    )
    {
        $this->userSecurityRepository = $userSecurityRepository;
        $this->router = $router;
    }

    public function authenticate(Request $request): Passport
    {

       $email = $request->request->get('email');
       $password = $request->request->get('password');

       return new Passport(
            new UserBadge($email),
            new PasswordCredentials($password),
            [
                new CsrfTokenBadge('authenticate',$request->request->get('_csrf_token')),
                (new RememberMeBadge())->enable()
            ]
       );
    }

    public function onAuthenticationSuccess(Request $request, TokenInterface $token, string $firewallName): ?Response
    {

        if($target = $this->getTargetPath($request->getSession(),$firewallName)){
            return new RedirectResponse($target);
        }
        return new RedirectResponse($this->router->generate('test'));
    }

    protected function getLoginUrl(Request $request): string
    {
        return $this->router->generate('user_login');
    }
}