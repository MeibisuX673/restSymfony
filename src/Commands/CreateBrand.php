<?php

namespace App\Commands;

use App\Entity\Brand;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(name: 'app:create-brand', description: 'create new Product')]

class CreateBrand extends Command
{

    public function __construct(
        private EntityManagerInterface $entityManager
    )
    {
        parent::__construct();

    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $brand = new Brand();
        $brand->name = $input->getArgument('brandName');
        $this->entityManager->persist($brand);
        $this->entityManager->flush();
        $output->writeln('Brand created');

        $io->success('ok');
        return Command::SUCCESS;
    }

    protected function configure()
    {
        $this->addArgument('brandName',InputArgument::REQUIRED,'brandName');
    }

}