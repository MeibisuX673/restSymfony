<?php

namespace App\Tests;



use App\Entity\User;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;



class UsersTest extends AbstractTest
{


    public function testGetCollectionAsUser(){

        $this->createClientWithCredentials()->request(Request::METHOD_GET,'/api/users');

        $this->assertResponseStatusCodeSame(Response::HTTP_OK);

        $this->assertJsonContains([
               '@context'=> '/api/contexts/User',
               '@id'=> '/api/users',
               '@type'=> 'hydra:Collection'
        ]);

        $this->assertMatchesResourceCollectionJsonSchema(User::class);
    }

    public function testCreate(){

        $response = static::createClient()->request(Request::METHOD_POST,'api/users',[
            'json'=>[
                'email'=>'example@example.com',
                'firstName'=>'firstName',
                'lastName'=>'lastName',
                'password'=>'test'
            ]
        ]);

        $this->assertResponseStatusCodeSame(Response::HTTP_CREATED);

        $this->assertJsonContains([
            "@context"=> "/api/contexts/User",
            "@type"=> "User",
            "email"=> "example@example.com",
            "firstName"=> "firstName",
            "lastName"=> "lastName",
        ]);

        $this->assertMatchesResourceItemJsonSchema(User::class);
    }

    public function testGetByIdAsUser(){


        $this->createClientWithCredentials()->request(Request::METHOD_GET,'/api/users/3');

        $this->assertResponseStatusCodeSame(Response::HTTP_OK);

        $this->assertJsonContains([
            '@context'=> '/api/contexts/User',
            '@id'=> '/api/users/3',
            '@type'=> 'User',
            'id'=> 3
        ]);

        $this->assertMatchesResourceItemJsonSchema(User::class);
    }

    public function testPutAsUser(){

        $user = $this->getUser();

        $response = $this->createClientWithCredentials()->request(Request::METHOD_PUT,'/api/users/' . $user->getId(),[
            'json'=>[
                'email'=>'test@test.com',
                'firstName'=>'test',
                'lastName'=>'X673'
            ]
        ]);

        $this->assertResponseStatusCodeSame(Response::HTTP_OK);

        $this->assertJsonContains([
            '@context'=> '/api/contexts/User',
            '@id'=> '/api/users/' . $user->getId(),
            '@type'=> 'User',
            'email'=> 'test@test.com',
            'firstName'=> 'test',
            'lastName'=> 'X673'
        ]);

        $this->assertMatchesResourceItemJsonSchema(User::class);
    }

    public function testDeleteAsUser(){

        $user = $this->getUser();

        $this->createClientWithCredentials()->request(Request::METHOD_DELETE,'/api/users/' . $user->getId());

        $this->assertResponseStatusCodeSame(Response::HTTP_NO_CONTENT);

    }

    public function testGetCollection(){

        static::createClient()->request(Request::METHOD_GET,'/api/users');

        $this->assertResponseStatusCodeSame(Response::HTTP_UNAUTHORIZED);

        $this->assertJsonContains([
            'code'=> 401,
            'message'=> 'JWT Token not found'

        ]);
    }

    public function testGetById(){

        $user = $this->getUser();

        static::createClient()->request(Request::METHOD_GET,'/api/users/' . $user->getId());

        $this->assertResponseStatusCodeSame(Response::HTTP_UNAUTHORIZED);

        $this->assertJsonContains([
            'code'=> 401,
            'message'=> 'JWT Token not found'
        ]);

    }

    public function testPut(){

        $user = $this->getUser();

        static::createClient()->request(Request::METHOD_PUT,'/api/users/' . $user->getId(),[
            'json'=>[
                'email'=>'test@test.com',
                'firstName'=>'test',
                'lastName'=>'X673'
            ]
        ]);

        $this->assertResponseStatusCodeSame(Response::HTTP_UNAUTHORIZED);

        $this->assertJsonContains([
            'code'=> 401,
            'message'=> 'JWT Token not found'
        ]);

    }

    public function testDelete(){

        $user = $this->getUser();

        static::createClient()->request(Request::METHOD_DELETE,'/api/users/' . $user->getId());

        $this->assertResponseStatusCodeSame(Response::HTTP_UNAUTHORIZED);

        $this->assertJsonContains([
            'code'=> 401,
            'message'=> 'JWT Token not found'
        ]);

    }

    public function testPutAlien(){

        $user = $this->getUser();
        $token = $this->getToken([
            'email'=>'noBrand@test.com',
            'password'=>'test'
        ]);

        $this->createClientWithCredentials($token)->request(Request::METHOD_PUT,'/api/users/' . $user->getId(),[
            'json'=>[
                'firstName'=> 'testCreate',
            ]
        ]);

        $this->assertResponseStatusCodeSame(Response::HTTP_FORBIDDEN);
    }

    public function testDeleteAlien(){

        $user = $this->getUser();
        $token = $this->getToken([
            'email'=>'noBrand@test.com',
            'password'=>'test'
        ]);

        $this->createClientWithCredentials($token)->request(Request::METHOD_DELETE,'/api/users/' . $user->getId());

        $this->assertResponseStatusCodeSame(Response::HTTP_FORBIDDEN);
    }

}