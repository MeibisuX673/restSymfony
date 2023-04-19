<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiProperty;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Delete;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Post;
use App\Controller\CreateProductMediaObjectController;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping AS ORM;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\Serializer\Annotation\Groups;
use Vich\UploaderBundle\Mapping\Annotation AS VICH;
use ApiPlatform\OpenApi\Model;

#[ApiResource(
    types: ['https://schema.org/MediaObject'],

    operations: [
        new Get(),
        new GetCollection(),
        new Post(
            controller: CreateProductMediaObjectController::class,
            openapi: new Model\Operation(
                requestBody: new Model\RequestBody(
                    content: new \ArrayObject([
                        'multipart/form-data' => [
                            'schema' => [
                                'type' => 'object',
                                'properties' => [
                                    'file' => [
                                        'type' => 'string',
                                        'format' => 'binary'
                                    ]

                                ]
                            ]
                        ]

                    ])
                )
            ),

            validationContext: ['groups'=>[self::SET_MEDIA_PRODUCT]],
            deserialize: false
        ),
        new Delete()
    ],
    normalizationContext: ['groups'=>[self::READ_MEDIA_PRODUCT]]
)]
#[ORM\Entity]
#[VICH\Uploadable]
#[ORM\HasLifecycleCallbacks]
class ProductMediaObject extends BaseEntity
{

    const READ_MEDIA_PRODUCT = 'READ_MEDIA_PRODUCT';
    const SET_MEDIA_PRODUCT = 'SET_MEDIA_PRODUCT';


    #[VICH\UploadableField(mapping: 'media_object', fileNameProperty: 'fileName',size: 'fileSize')]
    #[Groups([self::SET_MEDIA_PRODUCT])]
    public ?File $imageFile = null;

    #[ORM\Column(type: Types::STRING,nullable: false)]
    #[Groups([self::READ_MEDIA_PRODUCT])]
    public ?string $fileName;

    #[Groups([self::READ_MEDIA_PRODUCT])]
    #[ApiProperty(types: ['https://schema.org/filePath'])]
    #[ORM\Column(type: Types::STRING,nullable: true)]
    public ?string $filePath;

    #[Groups([self::READ_MEDIA_PRODUCT])]
    #[ORM\Column(type: Types::INTEGER,nullable: false)]
    public ?int $fileSize;





}