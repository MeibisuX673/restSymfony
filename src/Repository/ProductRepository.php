<?php

namespace App\Repository;

use App\Entity\Product;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class ProductRepository extends ServiceEntityRepository
{

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Product::class);
    }
    public function incrementTimesViewed(int $id): Product
    {

        $product = $this->find($id);
        $product->timesViewed++;

        $this->getEntityManager()->flush();

        return $product;

    }
}