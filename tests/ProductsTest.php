<?php

namespace App\Tests;



use ApiPlatform\Symfony\Bundle\Test\ApiTestCase;
use App\Entity\Product;
use Hautelook\AliceBundle\PhpUnit\RecreateDatabaseTrait;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class ProductsTest extends ApiTestCase
{

    use RecreateDatabaseTrait;

    public function testGetCollection(){

        $response = static::createClient()->request(Request::METHOD_GET,'/api/products');

        $this->assertResponseStatusCodeSame(Response::HTTP_OK);

        $this->assertResponseHeaderSame('content-type', 'application/ld+json; charset=utf-8');

        $this->assertJsonContains([
            "@context"=>"/api/contexts/Product",
            "@id"=> "/api/products",
            "@type"=> "hydra:Collection",
            "hydra:totalItems"=> 50
        ]);

        $this->assertCount(30,$response->toArray()['hydra:member']);

        $this->assertMatchesResourceItemJsonSchema(Product::class);

    }

    public function testCreate(){

        $response =  static::createClient()->request(Request::METHOD_POST,'/api/products',[
            'json'=>[
                'name'=>'testCreate',
                'brand'=>'/api/brands/9'
            ]
        ]);

        $this->assertResponseStatusCodeSame(Response::HTTP_CREATED);

        $this->assertResponseHeaderSame('content-type', 'application/ld+json; charset=utf-8');

        $this->assertJsonContains([
            "@context"=>"/api/contexts/Product",
            "@type"=> "Product",
            "name"=> "testCreate"
        ]);

        $this->assertMatchesRegularExpression('/^\/\w+\/products\/\d+$/', $response->toArray()['@id']);

        $this->assertMatchesResourceItemJsonSchema(Product::class);
    }

    public function testGetById(){

        static::createClient()->request(Request::METHOD_GET,'/api/products/2');

        $this->assertResponseStatusCodeSame(Response::HTTP_OK);

        $this->assertResponseHeaderSame('content-type', 'application/ld+json; charset=utf-8');


        $this->assertJsonContains([
            "@context"=> "/api/contexts/Product",
            "@id"=> "/api/products/2",
            "@type"=> "Product"
        ]);

        $this->assertMatchesResourceItemJsonSchema(Product::class);

    }

    public function testPut(){

        static::createClient()->request(Request::METHOD_PUT,'/api/products/2',[
            'json'=>[
                "name"=> "BANAN",
                "brand"=> "/api/brands/4"
            ]
        ]);

        $this->assertJsonContains([
            "@context"=>"/api/contexts/Product",
            "@id"=> "/api/products/2",
            "@type"=> "Product",
            "name"=> "BANAN",
            "brand"=> [
                "@id"=> "/api/brands/4",
                "@type"=> "Brand"
            ],
            "id"=>2
        ]);

        $this->assertResponseStatusCodeSame(Response::HTTP_OK);

        $this->assertMatchesResourceItemJsonSchema(Product::class);

    }


    public function testDelete(){

        static::createClient()->request(Request::METHOD_DELETE,'/api/products/2');

        $this->assertResponseStatusCodeSame(Response::HTTP_NO_CONTENT);

    }
}