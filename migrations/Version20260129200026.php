<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260129200026 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE contract CHANGE deplacement deplacement TINYINT DEFAULT NULL, CHANGE transport_fee_taken transport_fee_taken TINYINT DEFAULT NULL, CHANGE lunch_taken lunch_taken TINYINT DEFAULT NULL, CHANGE host_taken host_taken TINYINT DEFAULT NULL, CHANGE bonus bonus TINYINT DEFAULT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE contract CHANGE deplacement deplacement TINYINT NOT NULL, CHANGE transport_fee_taken transport_fee_taken TINYINT NOT NULL, CHANGE lunch_taken lunch_taken TINYINT NOT NULL, CHANGE host_taken host_taken TINYINT NOT NULL, CHANGE bonus bonus TINYINT NOT NULL');
    }
}
