<?php

namespace App\Tests;

use ApiPlatform\Symfony\Bundle\Test\ApiTestCase;
use App\Entity\Brand;
use App\Entity\Product;
use App\Entity\User;
use Hautelook\AliceBundle\PhpUnit\RecreateDatabaseTrait;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class BrandsTest extends AbstractTest
{

    public function testGetCollectionAsUser(){

        $this->createClientWithCredentials()->request(Request::METHOD_GET,'/api/brands');

        $this->assertResponseStatusCodeSame(Response::HTTP_OK);

        $this->assertResponseHeaderSame('content-type', 'application/ld+json; charset=utf-8');

        $this->assertJsonContains([
            '@context'=> '/api/contexts/Brand',
            '@id'=>  '/api/brands',
            '@type'=>  'hydra:Collection'
        ]);

        $this->assertMatchesResourceCollectionJsonSchema(Brand::class);
    }

    public function testCreateBrandAsUser(){

        $user = $this->getUser('noBrand@test.com');
        $token = $this->getToken([
            'email'=> 'noBrand@test.com',
            'password'=> 'test'
        ]);

        $this->createClientWithCredentials($token)->request(Request::METHOD_POST, '/api/brands', ['json' => [
            'name'=> 'brand1',
            'user'=> 'api/users/' . $user->getId()
        ]]);

        $this->assertResponseStatusCodeSame(Response::HTTP_CREATED);

        $this->assertResponseHeaderSame('content-type', 'application/ld+json; charset=utf-8');

        $this->assertJsonContains([
            '@context'=> '/api/contexts/Brand',
            '@type'=> 'Brand',
            'name'=> 'brand1',
        ]);

        $this->assertMatchesResourceItemJsonSchema(Brand::class);
    }

    public function testGetByIdAsUser(){

        $this->createClientWithCredentials()->request(Request::METHOD_GET,'/api/brands/3');

        $this->assertResponseStatusCodeSame(Response::HTTP_OK);

        $this->assertResponseHeaderSame('content-type', 'application/ld+json; charset=utf-8');

        $this->assertJsonContains([
            '@context'=> '/api/contexts/Brand',
            '@id'=> '/api/brands/3',
            '@type'=> 'Brand'

        ]);

        $this->assertMatchesResourceItemJsonSchema(Brand::class);
    }

    public function testPutAsUser(){

        $user = $this->getUser();

        $response = $this->createClientWithCredentials()->request(Request::METHOD_PUT,'/api/brands/' . $user->brand->getId() ,[
            'json'=>[
                'name'=>'brandPUT'
            ]
        ]);

        $this->assertResponseStatusCodeSame(Response::HTTP_OK);
        $this->assertResponseHeaderSame('content-type', 'application/ld+json; charset=utf-8');

        $this->assertJsonContains([
            '@context'=> '/api/contexts/Brand',
            '@type'=>  'Brand',
            'name'=>  'brandPUT',
            'id'=> $user->brand->getId()
        ]);
        $this->assertMatchesRegularExpression('/\/api\/brands\/\d+/', $response->toArray()['@id']);
        $this->assertMatchesResourceItemJsonSchema(Brand::class);

    }

    public function testDeleteAsUser()
    {

        $user = $this->getUser();

        $this->createClientWithCredentials()->request(Request::METHOD_DELETE, '/api/brands/' . $user->brand->getId());

        $this->assertResponseStatusCodeSame(Response::HTTP_NO_CONTENT);

    }

    public function testGetCollection(){

        static::createClient()->request(Request::METHOD_GET,'/api/brands');

        $this->assertResponseStatusCodeSame(Response::HTTP_UNAUTHORIZED);

        $this->assertJsonContains([
            'code'=> 401,
            'message'=> 'JWT Token not found'
        ]);

    }

    public function testGetById(){

        static::createClient()->request(Request::METHOD_GET,'/api/brands/3');

        $this->assertResponseStatusCodeSame(Response::HTTP_UNAUTHORIZED);

        $this->assertJsonContains([
            'code'=> 401,
            'message'=> 'JWT Token not found'

        ]);

    }

    public function testCreate(){

        static::createClient()->request(Request::METHOD_POST,'/api/brands',[
            'json'=>[
                'name'=> 'brand1',
                'user'=> '/api/users/3'
            ]

        ]);

        $this->assertResponseStatusCodeSame(Response::HTTP_UNAUTHORIZED);

        $this->assertJsonContains([
            'code'=> 401,
            'message'=> 'JWT Token not found'
        ]);
    }

    public function testPut(){

        static::createClient()->request(Request::METHOD_PUT,'/api/brands/3',[
            'json'=>[
                'name'=> 'brand1',
                'user'=> '/api/users/3'
            ]
        ]);

        $this->assertResponseStatusCodeSame(Response::HTTP_UNAUTHORIZED);

        $this->assertJsonContains([
            'code'=> 401,
            'message'=> 'JWT Token not found'
        ]);
    }

    public function testDelete(){

        static::createClient()->request(Request::METHOD_DELETE,'/api/brands/3');

        $this->assertResponseStatusCodeSame(Response::HTTP_UNAUTHORIZED);

        $this->assertJsonContains([
            'code'=> 401,
            'message'=> 'JWT Token not found'
        ]);
    }

    public function testPutAlien(){

        $this->createClientWithCredentials()->request(Request::METHOD_DELETE,'/api/brands/3',[
            'json'=> [
                'name'=> 'hooli'
            ]
        ]);

        $this->assertResponseStatusCodeSame(Response::HTTP_FORBIDDEN);
    }

    public function testDeleteAlien(){

        $this->createClientWithCredentials()->request(Request::METHOD_DELETE,'/api/brands/3');

        $this->assertResponseStatusCodeSame(Response::HTTP_FORBIDDEN);
    }
}