<?php

namespace App\Command;

use App\Entity\Category;
use App\Entity\Product;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(name: 'app:load-test-data')]
class LoadTestDataCommand extends Command
{
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        parent::__construct();
        $this->entityManager = $entityManager;
    }

    protected function configure(): void
    {
        $this->setDescription('Loads test data into the database.');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $output->writeln('Loading test data...');

        // Créer des catégories
        $chewingGum = new Category();
        $chewingGum->setName('Chewing-gums');
        $this->entityManager->persist($chewingGum);

        $pastilles = new Category();
        $pastilles->setName('Pastilles');
        $this->entityManager->persist($pastilles);

        $menthe = new Category();
        $menthe->setName('Menthe pure');
        $this->entityManager->persist($menthe);

        // Créer des produits
        $product1 = new Product();
        $product1->setName('Chewing-gum Menthe Fraîche')
            ->setPrice(1.99)
            ->setDescription('Un chewing-gum à la menthe pour une haleine fraîche.')
            ->setCategory($chewingGum);
        $this->entityManager->persist($product1);

        $product2 = new Product();
        $product2->setName('Pastilles Citron')
            ->setPrice(2.49)
            ->setDescription('Pastilles acidulées au citron.')
            ->setCategory($pastilles);
        $this->entityManager->persist($product2);

        $product3 = new Product();
        $product3->setName('Menthe Forte')
            ->setPrice(3.00)
            ->setCategory($menthe);
        $this->entityManager->persist($product3);

        $this->entityManager->flush();
        $output->writeln('Test data loaded successfully!');

        return Command::SUCCESS;
    }
}