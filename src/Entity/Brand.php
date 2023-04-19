<?php

namespace App\Entity;

use ApiPlatform\Doctrine\Orm\Filter\SearchFilter;
use ApiPlatform\Metadata\ApiFilter;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Delete;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Link;
use ApiPlatform\Metadata\Patch;
use ApiPlatform\Metadata\Post;
use ApiPlatform\Metadata\Put;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping AS ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints AS Assert;


#[ORM\HasLifecycleCallbacks]
#[ApiResource(

    normalizationContext: ['groups'=>[self::GET_MANY_BRAND,self::GET_ONE_BRAND,self::GET_MANY_BASE,self::GET_ONE_BASE]],
    denormalizationContext: ['groups'=>[self::SET_BRAND]],

)]
#[Get]
#[GetCollection]
#[Post]
#[Patch(securityPostDenormalize: 'object.user === user', securityPostDenormalizeMessage: 'у вас нет прав')]
#[Put(securityPostDenormalize: 'object.user === user', securityPostDenormalizeMessage: 'у вас нет прав')]
#[Delete(securityPostDenormalize: 'object.user === user', securityPostDenormalizeMessage: 'у вас нет прав')]
#[ApiFilter(SearchFilter::class,
   properties: [
       'name'=> SearchFilter::STRATEGY_PARTIAL
    ]
)]
#[ORM\Entity]
#[ORM\Table(name: '`brands`')]
class Brand extends BaseEntity
{
    const GET_ONE_BRAND = 'GET_ONE_BRAND';
    const GET_MANY_BRAND = 'GET_MANY_BRANDS';
    const SET_BRAND = 'SET_BRAND';

    const GET_SUBRESOURCE_BRAND = 'GET_SUBRESOURCE_BRAND';

    #[Groups([self::SET_BRAND,self::GET_MANY_BRAND,self::GET_ONE_BRAND,self::GET_SUBRESOURCE_BRAND, Product::GET_MANY_PRODUCT,Product::GET_ONE_PRODUCT])]
    #[ORM\Column(type: Types::STRING, nullable: false)]
    #[Assert\NotBlank]
    public string $name;

    #[ORM\OneToMany(mappedBy: 'brand', targetEntity: Product::class, cascade: ['remove'])]
    #[Groups([self::GET_ONE_BRAND,self::GET_MANY_BRAND])]
    public iterable $products;

    #[Assert\NotBlank]
    #[Assert\NotNull]
    #[ORM\OneToOne(inversedBy: 'brand', targetEntity: User::class, orphanRemoval: true)]
    #[Groups([self::SET_BRAND,self::GET_MANY_BRAND,self::GET_ONE_BRAND])]
    public User $user;

    public function __construct()
    {
        $this->products = new ArrayCollection();
    }

    public function addProduct(Product $product){
        if(!$this->products->contains($product)){
            $this->products->add($product);
        }
        $product->brand = $this;
        return $this;
    }

    public function removeProduct(Product $product){
        if($this->products->contains($product)){
            $this->products->remove($product->getId());
            $product->brand = null;
        }
        return $this;
    }

}