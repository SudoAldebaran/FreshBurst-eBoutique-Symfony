<?php

   namespace App\Command;

   use App\Entity\User;
   use Doctrine\ORM\EntityManagerInterface;
   use Symfony\Component\Console\Attribute\AsCommand;
   use Symfony\Component\Console\Command\Command;
   use Symfony\Component\Console\Input\InputInterface;
   use Symfony\Component\Console\Output\OutputInterface;
   use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

   #[AsCommand(name: 'app:create-admin')]
   class CreateAdminCommand extends Command
   {
       private $entityManager;
       private $passwordHasher;

       public function __construct(EntityManagerInterface $entityManager, UserPasswordHasherInterface $passwordHasher)
       {
           parent::__construct();
           $this->entityManager = $entityManager;
           $this->passwordHasher = $passwordHasher;
       }

       protected function configure(): void
       {
           $this->setDescription('Creates an admin user');
       }

       protected function execute(InputInterface $input, OutputInterface $output): int
       {
           $user = new User();
           $user->setEmail('admin@freshburst.com');
           $user->setName('Admin');
           $user->setPassword($this->passwordHasher->hashPassword($user, 'admin123'));
           $user->setRoles(['ROLE_ADMIN']);

           $this->entityManager->persist($user);
           $this->entityManager->flush();

           $output->writeln('Admin user created: admin@freshburst.com / admin123');

           return Command::SUCCESS;
       }
   }