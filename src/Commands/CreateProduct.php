<?php

namespace App\Commands;

use App\Entity\Brand;
use app\Entity\Product;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(name: 'app:create-product',description: 'create product')]
class CreateProduct extends Command {


    public function __construct
    (
        private EntityManagerInterface $entityManager
    )
    {
        parent::__construct();
    }

    public function execute(InputInterface $input, OutputInterface $output): int
    {
        /**
         * @var Brand $brand
         */
        $brand = $this->entityManager->getRepository(Brand::class)->findOneBy(['id' => 1]);

        if(!$brand){
            $output->writeln('brend not found');
            return Command::INVALID;
        }

        $product = new Product();
        $product->name = $input->getArgument('productName');
        $product->brand = $brand;

        $this->entityManager->persist($product);
        $this->entityManager->flush();

        $output->writeln('Product created');
        return Command::SUCCESS;

    }

    public function configure()
    {

        $this->addArgument('productName',InputArgument::REQUIRED,'productName');

    }

}