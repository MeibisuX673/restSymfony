<?php

namespace App\Tests;


use App\Entity\User;
use Hautelook\AliceBundle\PhpUnit\RecreateDatabaseTrait;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class UsersTest extends AbstractTest
{

    use RecreateDatabaseTrait;

    public function testGetCollection(){

        $response = static::createClient()->request(Request::METHOD_GET,'/api/users');

        $this->assertResponseStatusCodeSame(Response::HTTP_OK);

        $this->assertJsonContains([
            '@context'=> '/api/contexts/User',
            '@id'=> '/api/users',
            '@type'=> 'hydra:Collection',
        ]);

        $this->assertMatchesResourceItemJsonSchema(User::class);
    }

    public function testCreate(){

        $response = static::createClient()->request(Request::METHOD_POST,'api/users',[
            'json'=>[
                'email'=>'example@test.com',
                'firstName'=>'firstName',
                'lastName'=>'lastName',
                'password'=>'test'
            ]
        ]);

        $this->assertResponseStatusCodeSame(Response::HTTP_CREATED);

        $this->assertJsonContains([
            "@context"=> "/api/contexts/User",
            "@id"=> "/api/users/12",
            "@type"=> "User",
            "email"=> "user5@example.com",
            "firstName"=> "firstName",
            "lastName"=> "lastName",
        ]);
    }



}