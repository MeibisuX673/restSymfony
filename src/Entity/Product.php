<?php

namespace App\Entity;


use ApiPlatform\Doctrine\Orm\Filter\NumericFilter;
use ApiPlatform\Doctrine\Orm\Filter\OrderFilter;
use ApiPlatform\Doctrine\Orm\Filter\RangeFilter;
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
use App\Filter\CategoryFilter;
use App\Filter\CustomFilter;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;


#[ApiResource(
    normalizationContext: ['groups' => [self::GET_ONE_BASE, self::GET_MANY_BASE, self::GET_ONE_PRODUCT, self::GET_MANY_PRODUCT]],
    denormalizationContext: ['groups' => [self::SET_PRODUCT]],
)]
#[Post(securityPostDenormalize: 'object.brand.user === user', securityPostDenormalizeMessage: 'у вас нет прав')]
#[Put(securityPostDenormalize: 'object.brand.user === user', securityPostDenormalizeMessage: 'у вас нет прав')]
#[Patch(securityPostDenormalize: 'object.brand.user === user', securityPostDenormalizeMessage: 'у вас нет прав')]
#[Delete(securityPostDenormalize: 'object.brand.user === user', securityPostDenormalizeMessage: 'у вас нет прав')]
#[Get]
#[GetCollection]

#[ApiResource(
    uriTemplate: '/brands/{id}/products',
    operations: [new GetCollection()],
    uriVariables: [
        'id' => new Link(
            fromProperty: 'products',
            fromClass: Brand::class
        )
    ],
    normalizationContext: ['groups' => [self::GET_SUBRESOURCE_PRODUCT, self::GET_MANY_BASE]]
)]
#[ApiFilter(
    RangeFilter::class,
    properties: [
        'price'
    ]
)]
#[ApiFilter(
    SearchFilter::class,
    properties: [
        'name' => SearchFilter::STRATEGY_PARTIAL,
        'brands.name' => SearchFilter::STRATEGY_START
    ]
)]
#[ApiFilter(
    OrderFilter::class,
    properties: ['price' => 'ASC']
)]
#[ApiFilter(
    NumericFilter::class,
    properties: ['brand.id']
)]
#[ApiFilter(
    CustomFilter::class,
    properties: [
        'visible'
    ]
)]
#[ApiFilter(
    CategoryFilter::class
)]
#[ORM\Entity]
#[ORM\Table(name: '`products`')]
#[ORM\HasLifecycleCallbacks]
class Product extends BaseEntity
{

    const GET_MANY_PRODUCT = 'GET_MANY_PRODUCT';
    const GET_ONE_PRODUCT = 'GET_ONE_PRODUCT';
    const SET_PRODUCT = 'SET_PRODUCT';

    const GET_SUBRESOURCE_PRODUCT = 'GET_SUBRESOURCE_PRODUCT';


    public function __construct()
    {
        $this->mediaObjects = new ArrayCollection();
    }

    #[Groups([Brand::GET_ONE_BRAND, Brand::GET_MANY_BRAND, self::GET_ONE_PRODUCT, self::GET_MANY_PRODUCT, self::SET_PRODUCT, self::GET_SUBRESOURCE_PRODUCT])]
    #[ORM\Column(type: Types::STRING, nullable: false)]
    #[Assert\NotBlank]
    public string $name;

    #[Groups([self::GET_ONE_PRODUCT, self::GET_MANY_PRODUCT, self::SET_PRODUCT])]
    #[ORM\ManyToOne(targetEntity: Brand::class, inversedBy: 'products')]
    #[ORM\JoinColumn(name: 'brand_id', referencedColumnName: 'id', onDelete: 'SET NULL')]
    #[Assert\NotBlank]
    public ?Brand $brand;

    #[ORM\Column(type: Types::INTEGER, nullable: false)]
    #[Groups([self::GET_ONE_PRODUCT, self::GET_MANY_PRODUCT, self::SET_PRODUCT, Brand::GET_ONE_BRAND, Brand::GET_MANY_BRAND])]
    #[Assert\NotBlank]
    public int $price;

    #[ORM\Column(type: Types::BOOLEAN, nullable: false)]
    #[Groups([self::GET_ONE_PRODUCT, self::GET_MANY_PRODUCT, self::SET_PRODUCT, Brand::GET_ONE_BRAND, Brand::GET_MANY_BRAND])]
    #[Assert\NotBlank]
    public bool $visible;

    #[ORM\Column(type: Types::INTEGER, nullable: false)]
    #[Groups([self::GET_ONE_PRODUCT, self::GET_MANY_PRODUCT, self::SET_PRODUCT, Brand::GET_ONE_BRAND, Brand::GET_MANY_BRAND])]
    #[Assert\NotBlank]
    public int $amount;

    #[ORM\Column(type: Types::INTEGER, nullable: false)]
    #[Groups([self::GET_ONE_PRODUCT, self::GET_MANY_PRODUCT, Brand::GET_ONE_BRAND, Brand::GET_MANY_BRAND])]
    public int $timesViewed = 0;

    #[ORM\JoinTable(name: 'product_files')]
    #[ORM\JoinColumn(name: 'product_id', referencedColumnName: 'id',)]
    #[ORM\InverseJoinColumn(name: 'file_id', referencedColumnName: 'id', unique: true)]
    #[ORM\ManyToMany(targetEntity: ProductMediaObject::class, cascade: ['remove'])]
    #[Groups([self::GET_ONE_PRODUCT, self::GET_MANY_PRODUCT, self::SET_PRODUCT])]
    public iterable $mediaObjects;

    public function addMediaObject(ProductMediaObject $mediaObject)
    {
        if (!$this->mediaObjects->contains($mediaObject)) {
            $this->mediaObjects->add($mediaObject);
            $mediaObject->product = $this;
        }
        return $this;
    }

    public function removeMediaObject(ProductMediaObject $mediaObject)
    {
        if ($this->mediaObjects->contains($mediaObject)) {
            $this->mediaObjects->remove($mediaObject->getId());
            $mediaObject->product = null;
        }
        return $this;
    }


}