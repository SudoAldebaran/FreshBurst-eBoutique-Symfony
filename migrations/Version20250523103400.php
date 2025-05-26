<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20250523103400 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Update roles column to ensure non-null JSON values';
    }

    public function up(Schema $schema): void
    {
        // Met à jour les lignes existantes pour remplacer NULL par un tableau JSON vide
        $this->addSql("UPDATE user SET roles = '[]' WHERE roles IS NULL");
    }

    public function down(Schema $schema): void
    {
        // Pas d’action nécessaire pour le rollback
    }
}