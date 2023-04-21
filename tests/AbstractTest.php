<?php

namespace App\Tests;

use ApiPlatform\Symfony\Bundle\Test\ApiTestCase;
use App\Entity\User;
use Hautelook\AliceBundle\PhpUnit\RefreshDatabaseTrait;
use ApiPlatform\Symfony\Bundle\Test\Client;
use Symfony\Component\HttpFoundation\Request;

abstract class AbstractTest extends ApiTestCase
{
    private ?string $token = null;

    use RefreshDatabaseTrait;

    public function setUp(): void
    {
        self::bootKernel();
    }

    protected function createClientWithCredentials($token = null): Client
    {
        $token = $token ?: $this->getToken();
        return static::createClient([], ['headers' => ['authorization' => 'Bearer '.$token]]);
    }


    protected function getToken($body = []): string
    {
        if ($this->token) {
            return $this->token;
        }

        $response = static::createClient()->request(Request::METHOD_POST, '/auth', [
            'json' => $body ?: [
            'email' => 'test@test.com',
            'password' => 'test'
        ]]);

        $this->assertResponseIsSuccessful();
        $data = $response->toArray();
        $this->token = $data['token'];

        return $data['token'];
    }

    public function getUser(string $email = null): object{

        $user = static::getContainer()->get('doctrine')->getRepository(User::class)->findOneBy([
            'email'=> $email ?: 'test@test.com'
        ]);

        return $user;
    }

}