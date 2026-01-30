<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260129155559 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE contract ADD address_intern_ship VARCHAR(255) NOT NULL, ADD postal_code_internship VARCHAR(15) NOT NULL, ADD city_internship VARCHAR(255) NOT NULL, ADD country_internship VARCHAR(255) NOT NULL');
        $this->addSql('ALTER TABLE organisation DROP address_intern_ship, DROP postal_code_internship, DROP city_internship, DROP country_internship');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE contract DROP address_intern_ship, DROP postal_code_internship, DROP city_internship, DROP country_internship');
        $this->addSql('ALTER TABLE organisation ADD address_intern_ship VARCHAR(255) NOT NULL, ADD postal_code_internship VARCHAR(15) NOT NULL, ADD city_internship VARCHAR(255) NOT NULL, ADD country_internship VARCHAR(255) NOT NULL');
    }
}
