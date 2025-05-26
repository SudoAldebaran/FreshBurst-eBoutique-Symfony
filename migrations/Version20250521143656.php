<?php

   declare(strict_types=1);

   namespace DoctrineMigrations;

   use Doctrine\DBAL\Schema\Schema;
   use Doctrine\Migrations\AbstractMigration;

   final class Version20250521132400 extends AbstractMigration
   {
       public function getDescription(): string
       {
           return 'Add unique constraint on email in user table';
       }

       public function up(Schema $schema): void
       {
           $this->addSql('ALTER TABLE user ADD CONSTRAINT UNIQ_USER_EMAIL UNIQUE (email)');
       }

       public function down(Schema $schema): void
       {
           $this->addSql('ALTER TABLE user DROP INDEX UNIQ_USER_EMAIL');
       }
   }