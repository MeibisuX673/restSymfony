<?php

namespace App\Tests;



use ApiPlatform\Symfony\Bundle\Test\ApiTestCase;
use App\Entity\Product;
use App\Entity\User;
use Hautelook\AliceBundle\PhpUnit\RecreateDatabaseTrait;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class ProductsTest extends AbstractTest
{

    public function testGetCollectionAsUser(){

        $this->createClientWithCredentials()->request(Request::METHOD_GET,'/api/products?visible=true');

        $this->assertResponseStatusCodeSame(Response::HTTP_OK);

        $this->assertResponseHeaderSame('content-type', 'application/ld+json; charset=utf-8');

        $this->assertJsonContains([
            "@context"=>"/api/contexts/Product",
            "@id"=> "/api/products",
            "@type"=> "hydra:Collection",
        ]);

        $this->assertMatchesResourceCollectionJsonSchema(Product::class);

    }

    public function testCreateAsUser(){

        $user = $this->getUser();

        $response =  $this->createClientWithCredentials()->request(Request::METHOD_POST,'/api/products',[
            'json'=>[
                'name'=> 'testCreate',
                'brand'=> '/api/brands/' . $user->brand->getId(),
                'price'=> 0,
                'visible'=> true,
                'amount'=> 0,
            ]
        ]);

        $this->assertResponseStatusCodeSame(Response::HTTP_CREATED);

        $this->assertResponseHeaderSame('content-type', 'application/ld+json; charset=utf-8');

        $this->assertJsonContains([
            '@context'=>'/api/contexts/Product',
            '@type'=> 'Product',
            'name'=> 'testCreate',
            'price'=> 0,
            'visible'=> true,
            'amount'=> 0
        ]);

        $this->assertMatchesRegularExpression('/^\/\w+\/products\/\d+$/', $response->toArray()['@id']);

        $this->assertMatchesResourceItemJsonSchema(Product::class);
    }

    public function testGetByIdAsUser(){

        $this->createClientWithCredentials()->request(Request::METHOD_GET,'/api/products/2');

        $this->assertResponseStatusCodeSame(Response::HTTP_OK);

        $this->assertResponseHeaderSame('content-type', 'application/ld+json; charset=utf-8');


        $this->assertJsonContains([
            "@context"=> "/api/contexts/Product",
            "@id"=> "/api/products/2",
            "@type"=> "Product"
        ]);

        $this->assertMatchesResourceItemJsonSchema(Product::class);

    }

    public function testPutAsUser(){

        $user = $this->getUser();

        $this->createClientWithCredentials()->request(Request::METHOD_PUT,'/api/products/' . $user->brand->products[1]->getId(),[
            'json'=>[
                'name'=> 'HooliPhone(poop)',
                'brand'=> '/api/brands/' . $user->brand->getId()
            ]
        ]);

        $this->assertJsonContains([
            '@context'=>'/api/contexts/Product',
            '@id'=> '/api/products/' . $user->brand->products[1]->getId(),
            '@type'=>'Product',
            'name'=> 'HooliPhone(poop)',
            'brand'=> [
                '@id'=> '/api/brands/' . $user->brand->getId(),
                '@type'=> 'Brand'
            ],
            'id'=>$user->brand->products[1]->getId()
        ]);

        $this->assertResponseStatusCodeSame(Response::HTTP_OK);

        $this->assertMatchesResourceItemJsonSchema(Product::class);

    }

    public function testDeleteAsUser(){

        $user = $this->getUser();

        $this->createClientWithCredentials()->request(Request::METHOD_DELETE,'/api/products/' . $user->brand->products[1]->getId());

        $this->assertResponseStatusCodeSame(Response::HTTP_NO_CONTENT);

    }

    public function testGetCollection(){

        static::createClient()->request(Request::METHOD_GET,'/api/products');

        $this->assertResponseStatusCodeSame(Response::HTTP_UNAUTHORIZED);

        $this->assertJsonContains([
            'code'=> 401,
            'message'=> 'JWT Token not found'
        ]);
    }

    public function testGetById(){

        static::createClient()->request(Request::METHOD_GET,'/api/products/2');

        $this->assertResponseStatusCodeSame(Response::HTTP_UNAUTHORIZED);

        $this->assertJsonContains([
            'code'=> 401,
            'message'=> 'JWT Token not found'
        ]);
    }

    public function testCreate(){

        static::createClient()->request(Request::METHOD_POST,'/api/products',[
            'json'=>[
                'name'=> 'testCreate',
                'brand'=> '/api/brands/2',
                'price'=> 0,
                'visible'=> true,
                'amount'=> 0,
            ]
        ]);

        $this->assertResponseStatusCodeSame(Response::HTTP_UNAUTHORIZED);

        $this->assertJsonContains([
            'code'=> 401,
            'message'=> 'JWT Token not found'
        ]);

    }

    public function testPut(){

        static::createClient()->request(Request::METHOD_PUT,'/api/products/2',[
            'json'=>[
                'name'=> 'testCreate',

            ]
        ]);

        $this->assertResponseStatusCodeSame(Response::HTTP_UNAUTHORIZED);

        $this->assertJsonContains([
            'code'=> 401,
            'message'=> 'JWT Token not found'
        ]);

    }

    public function testDelete(){

        static::createClient()->request(Request::METHOD_DELETE,'/api/products/2');

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

        $this->createClientWithCredentials($token)->request(Request::METHOD_PUT,'/api/products/' . $user->brand->products[1]->getId(),[
            'json'=>[
                'name'=> 'testCreate',

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

        $this->createClientWithCredentials($token)->request(Request::METHOD_DELETE,'/api/products/' . $user->brand->products[1]->getId());

        $this->assertResponseStatusCodeSame(Response::HTTP_FORBIDDEN);

    }
}