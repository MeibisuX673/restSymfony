<?php

namespace App\Controller;

use App\Entity\ProductMediaObject;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Attribute\AsController;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;


final class CreateProductMediaObjectController extends AbstractController
{

    public function __invoke(Request $request): ProductMediaObject{

        $uploadFile = $request->files->get('file');

        if(!$uploadFile){
            throw new BadRequestHttpException('"file" is required');
        }

        $mediaObject = new ProductMediaObject();
        $mediaObject->imageFile = $uploadFile;

        return $mediaObject;
    }

}