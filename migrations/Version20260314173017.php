<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260314173017 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE contract (id INT AUTO_INCREMENT NOT NULL, status INT NOT NULL, place_name_internship VARCHAR(255) DEFAULT NULL, address_intern_ship VARCHAR(255) DEFAULT NULL, postal_code_internship VARCHAR(15) DEFAULT NULL, city_internship VARCHAR(255) DEFAULT NULL, country_internship VARCHAR(255) DEFAULT NULL, deplacement TINYINT DEFAULT NULL, transport_fee_taken TINYINT DEFAULT NULL, lunch_taken TINYINT DEFAULT NULL, host_taken TINYINT DEFAULT NULL, bonus TINYINT DEFAULT NULL, work_hours VARCHAR(3000) DEFAULT NULL, planned_activities VARCHAR(8000) DEFAULT NULL, sharing_token VARCHAR(255) DEFAULT NULL, token_exp_date DATETIME DEFAULT NULL, pdf_unsigned VARCHAR(255) DEFAULT NULL, pdf_signed VARCHAR(255) DEFAULT NULL, signature_request_id VARCHAR(255) DEFAULT NULL, signed_contract_path VARCHAR(255) DEFAULT NULL, student_id INT NOT NULL, tutor_id INT DEFAULT NULL, session_id INT NOT NULL, organisation_id INT DEFAULT NULL, INDEX IDX_E98F2859CB944F1A (student_id), INDEX IDX_E98F2859208F64F1 (tutor_id), INDEX IDX_E98F2859613FECDF (session_id), INDEX IDX_E98F28599E6B1585 (organisation_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE contract_date (id INT AUTO_INCREMENT NOT NULL, start_date DATE NOT NULL, end_date DATE NOT NULL, contract_id INT NOT NULL, INDEX IDX_C2EB79122576E0FD (contract_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE level (id INT AUTO_INCREMENT NOT NULL, level_code VARCHAR(20) DEFAULT NULL, level_name VARCHAR(255) NOT NULL, PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE organisation (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, address_hq VARCHAR(255) NOT NULL, postal_code_hq VARCHAR(15) NOT NULL, city_hq VARCHAR(255) NOT NULL, country_hq VARCHAR(255) NOT NULL, website VARCHAR(320) DEFAULT NULL, siret VARCHAR(40) NOT NULL, resp_firstname VARCHAR(255) NOT NULL, resp_lastname VARCHAR(255) NOT NULL, resp_function VARCHAR(255) NOT NULL, resp_email VARCHAR(320) NOT NULL, resp_phone VARCHAR(15) NOT NULL, insurance_name VARCHAR(255) NOT NULL, insurance_contract VARCHAR(255) NOT NULL, PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE parameters (id INT AUTO_INCREMENT NOT NULL, provisor_name VARCHAR(255) DEFAULT NULL, provisor_email VARCHAR(320) DEFAULT NULL, ddfpt_name VARCHAR(255) DEFAULT NULL, ddfpt_email VARCHAR(320) DEFAULT NULL, ddfpt_tel VARCHAR(15) DEFAULT NULL, student_email_domain VARCHAR(255) DEFAULT NULL, professor_email_domain VARCHAR(255) DEFAULT NULL, provisor_mobile_phone VARCHAR(15) DEFAULT NULL, school_address VARCHAR(3000) DEFAULT NULL, PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE reset_password_request (id INT AUTO_INCREMENT NOT NULL, selector VARCHAR(20) NOT NULL, hashed_token VARCHAR(100) NOT NULL, requested_at DATETIME NOT NULL, expires_at DATETIME NOT NULL, user_id INT NOT NULL, INDEX IDX_7CE748AA76ED395 (user_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE session (id INT AUTO_INCREMENT NOT NULL, active TINYINT DEFAULT 1 NOT NULL, level_id INT NOT NULL, INDEX IDX_D044D5D45FB14BA7 (level_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE session_date (id INT AUTO_INCREMENT NOT NULL, start_date DATE NOT NULL, end_date DATE NOT NULL, session_id INT NOT NULL, INDEX IDX_6CEF3750613FECDF (session_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE `user` (id INT AUTO_INCREMENT NOT NULL, email VARCHAR(180) NOT NULL, roles JSON NOT NULL, password VARCHAR(255) DEFAULT NULL, firstname VARCHAR(255) NOT NULL, lastname VARCHAR(255) NOT NULL, is_verified TINYINT NOT NULL, mobile_phone VARCHAR(15) DEFAULT NULL, discr VARCHAR(255) NOT NULL, personal_email VARCHAR(320) DEFAULT NULL, level_id INT DEFAULT NULL, tel_mobile VARCHAR(15) DEFAULT NULL, tel_other VARCHAR(15) DEFAULT NULL, work_function VARCHAR(255) DEFAULT NULL, INDEX IDX_8D93D6495FB14BA7 (level_id), UNIQUE INDEX UNIQ_IDENTIFIER_EMAIL (email), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE professor_level (professor_id INT NOT NULL, level_id INT NOT NULL, INDEX IDX_129D61E87D2D84D5 (professor_id), INDEX IDX_129D61E85FB14BA7 (level_id), PRIMARY KEY (professor_id, level_id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE level_referent_professors (professor_id INT NOT NULL, level_id INT NOT NULL, INDEX IDX_DDD82C8C7D2D84D5 (professor_id), INDEX IDX_DDD82C8C5FB14BA7 (level_id), PRIMARY KEY (professor_id, level_id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE messenger_messages (id BIGINT AUTO_INCREMENT NOT NULL, body LONGTEXT NOT NULL, headers LONGTEXT NOT NULL, queue_name VARCHAR(190) NOT NULL, created_at DATETIME NOT NULL, available_at DATETIME NOT NULL, delivered_at DATETIME DEFAULT NULL, INDEX IDX_75EA56E0FB7336F0E3BD61CE16BA31DBBF396750 (queue_name, available_at, delivered_at, id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('ALTER TABLE contract ADD CONSTRAINT FK_E98F2859CB944F1A FOREIGN KEY (student_id) REFERENCES `user` (id)');
        $this->addSql('ALTER TABLE contract ADD CONSTRAINT FK_E98F2859208F64F1 FOREIGN KEY (tutor_id) REFERENCES `user` (id)');
        $this->addSql('ALTER TABLE contract ADD CONSTRAINT FK_E98F2859613FECDF FOREIGN KEY (session_id) REFERENCES session (id)');
        $this->addSql('ALTER TABLE contract ADD CONSTRAINT FK_E98F28599E6B1585 FOREIGN KEY (organisation_id) REFERENCES organisation (id)');
        $this->addSql('ALTER TABLE contract_date ADD CONSTRAINT FK_C2EB79122576E0FD FOREIGN KEY (contract_id) REFERENCES contract (id)');
        $this->addSql('ALTER TABLE reset_password_request ADD CONSTRAINT FK_7CE748AA76ED395 FOREIGN KEY (user_id) REFERENCES `user` (id)');
        $this->addSql('ALTER TABLE session ADD CONSTRAINT FK_D044D5D45FB14BA7 FOREIGN KEY (level_id) REFERENCES level (id)');
        $this->addSql('ALTER TABLE session_date ADD CONSTRAINT FK_6CEF3750613FECDF FOREIGN KEY (session_id) REFERENCES session (id)');
        $this->addSql('ALTER TABLE `user` ADD CONSTRAINT FK_8D93D6495FB14BA7 FOREIGN KEY (level_id) REFERENCES level (id)');
        $this->addSql('ALTER TABLE professor_level ADD CONSTRAINT FK_129D61E87D2D84D5 FOREIGN KEY (professor_id) REFERENCES `user` (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE professor_level ADD CONSTRAINT FK_129D61E85FB14BA7 FOREIGN KEY (level_id) REFERENCES level (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE level_referent_professors ADD CONSTRAINT FK_DDD82C8C7D2D84D5 FOREIGN KEY (professor_id) REFERENCES `user` (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE level_referent_professors ADD CONSTRAINT FK_DDD82C8C5FB14BA7 FOREIGN KEY (level_id) REFERENCES level (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE contract DROP FOREIGN KEY FK_E98F2859CB944F1A');
        $this->addSql('ALTER TABLE contract DROP FOREIGN KEY FK_E98F2859208F64F1');
        $this->addSql('ALTER TABLE contract DROP FOREIGN KEY FK_E98F2859613FECDF');
        $this->addSql('ALTER TABLE contract DROP FOREIGN KEY FK_E98F28599E6B1585');
        $this->addSql('ALTER TABLE contract_date DROP FOREIGN KEY FK_C2EB79122576E0FD');
        $this->addSql('ALTER TABLE reset_password_request DROP FOREIGN KEY FK_7CE748AA76ED395');
        $this->addSql('ALTER TABLE session DROP FOREIGN KEY FK_D044D5D45FB14BA7');
        $this->addSql('ALTER TABLE session_date DROP FOREIGN KEY FK_6CEF3750613FECDF');
        $this->addSql('ALTER TABLE `user` DROP FOREIGN KEY FK_8D93D6495FB14BA7');
        $this->addSql('ALTER TABLE professor_level DROP FOREIGN KEY FK_129D61E87D2D84D5');
        $this->addSql('ALTER TABLE professor_level DROP FOREIGN KEY FK_129D61E85FB14BA7');
        $this->addSql('ALTER TABLE level_referent_professors DROP FOREIGN KEY FK_DDD82C8C7D2D84D5');
        $this->addSql('ALTER TABLE level_referent_professors DROP FOREIGN KEY FK_DDD82C8C5FB14BA7');
        $this->addSql('DROP TABLE contract');
        $this->addSql('DROP TABLE contract_date');
        $this->addSql('DROP TABLE level');
        $this->addSql('DROP TABLE organisation');
        $this->addSql('DROP TABLE parameters');
        $this->addSql('DROP TABLE reset_password_request');
        $this->addSql('DROP TABLE session');
        $this->addSql('DROP TABLE session_date');
        $this->addSql('DROP TABLE `user`');
        $this->addSql('DROP TABLE professor_level');
        $this->addSql('DROP TABLE level_referent_professors');
        $this->addSql('DROP TABLE messenger_messages');
    }
}
