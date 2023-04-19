<?php

namespace App\Tests;

use ApiPlatform\Symfony\Bundle\Test\ApiTestCase;
use App\Entity\Brand;
use App\Entity\Product;
use Hautelook\AliceBundle\PhpUnit\RecreateDatabaseTrait;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class BrandsTest extends ApiTestCase
{
    use RecreateDatabaseTrait;


    public function testGetCollection(){

        $response = static::createClient()->request(Request::METHOD_GET,'/api/brands');;

        $this->assertResponseStatusCodeSame(Response::HTTP_OK);

        $this->assertResponseHeaderSame('content-type', 'application/ld+json; charset=utf-8');

        $this->assertJsonContains([
            "@context"=> "/api/contexts/Brand",
            "@id"=>  "/api/brands",
            "@type"=>  "hydra:Collection",
            "hydra:totalItems"=> 10
        ]);

        $this->assertCount(10, $response->toArray()['hydra:member']);

        $this->assertMatchesResourceCollectionJsonSchema(Brand::class);
    }

    public function testCreateBrand(){

        $response = static::createClient()->request(Request::METHOD_POST, '/api/brands', ['json' => [
            'name'=>'brand1'
        ]]);

        $this->assertResponseStatusCodeSame(Response::HTTP_CREATED);

        $this->assertResponseHeaderSame('content-type', 'application/ld+json; charset=utf-8');

        $this->assertJsonContains([
            "@context"=> "/api/contexts/Brand",
            "@type"=> "Brand",
            "name"=> "brand1"
        ]);


        $this->assertMatchesRegularExpression('/^\/\w+\/brands\/\d+$/', $response->toArray()['@id']);

        $this->assertMatchesResourceItemJsonSchema(Brand::class);
    }

    public function testGetById(){

        $response = static::createClient()->request(Request::METHOD_GET,'/api/brands/5');

        $this->assertResponseStatusCodeSame(Response::HTTP_OK);

        $this->assertResponseHeaderSame('content-type', 'application/ld+json; charset=utf-8');

        $this->assertJsonContains([
            "@context"=> "/api/contexts/Brand",
            "@id"=> "/api/brands/5",
            "@type"=> "Brand"

        ]);

        $this->assertMatchesRegularExpression('/\w[^0-9]+/',$response->toArray()['name']);

        $this->assertMatchesResourceItemJsonSchema(Brand::class);
    }

    public function testPut(){

        static::createClient()->request(Request::METHOD_PUT,'/api/brands/5',[
            'json'=>[
                'name'=>'brandPUT'
            ]
        ]);

        $this->assertResponseStatusCodeSame(Response::HTTP_OK);
        $this->assertResponseHeaderSame('content-type', 'application/ld+json; charset=utf-8');

        $this->assertJsonContains([
            "@context"=> "/api/contexts/Brand",
            "@id"=>  "/api/brands/5",
            "@type"=>  "Brand",
            "name"=>  "brandPUT",
            "id"=> 5
        ]);

        $this->assertMatchesResourceItemJsonSchema(Brand::class);

    }


    public function testDelete(){

        static::createClient()->request(Request::METHOD_DELETE,'/api/brands/5');

        $this->assertResponseStatusCodeSame(Response::HTTP_NO_CONTENT);

    }

}