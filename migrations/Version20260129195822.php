<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260129195822 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE contract CHANGE work_hours work_hours VARCHAR(255) DEFAULT NULL, CHANGE planned_activities planned_activities VARCHAR(8000) DEFAULT NULL, CHANGE address_intern_ship address_intern_ship VARCHAR(255) DEFAULT NULL, CHANGE postal_code_internship postal_code_internship VARCHAR(15) DEFAULT NULL, CHANGE city_internship city_internship VARCHAR(255) DEFAULT NULL, CHANGE country_internship country_internship VARCHAR(255) DEFAULT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE contract CHANGE address_intern_ship address_intern_ship VARCHAR(255) NOT NULL, CHANGE postal_code_internship postal_code_internship VARCHAR(15) NOT NULL, CHANGE city_internship city_internship VARCHAR(255) NOT NULL, CHANGE country_internship country_internship VARCHAR(255) NOT NULL, CHANGE work_hours work_hours VARCHAR(255) NOT NULL, CHANGE planned_activities planned_activities VARCHAR(8000) NOT NULL');
    }
}
