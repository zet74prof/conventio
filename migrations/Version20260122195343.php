<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260122195343 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE parameters CHANGE provisor_name provisor_name VARCHAR(255) DEFAULT NULL, CHANGE provisor_email provisor_email VARCHAR(320) DEFAULT NULL, CHANGE ddfpt_name ddfpt_name VARCHAR(255) DEFAULT NULL, CHANGE ddfpt_email ddfpt_email VARCHAR(320) DEFAULT NULL, CHANGE ddfpt_tel ddfpt_tel VARCHAR(15) DEFAULT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE parameters CHANGE provisor_name provisor_name VARCHAR(255) NOT NULL, CHANGE provisor_email provisor_email VARCHAR(320) NOT NULL, CHANGE ddfpt_name ddfpt_name VARCHAR(255) NOT NULL, CHANGE ddfpt_email ddfpt_email VARCHAR(320) NOT NULL, CHANGE ddfpt_tel ddfpt_tel VARCHAR(15) NOT NULL');
    }
}
