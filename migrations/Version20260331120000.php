<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * MySQL 8+ compatible DDL (AUTO_INCREMENT, LONGTEXT, JSON — not SQLite AUTOINCREMENT/CLOB).
 */
final class Version20260331120000 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Create user and registered_doctor tables';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('CREATE TABLE registered_doctor (id INT AUTO_INCREMENT NOT NULL, room_type VARCHAR(10) NOT NULL, first_name VARCHAR(100) DEFAULT NULL, last_name VARCHAR(100) DEFAULT NULL, email VARCHAR(180) DEFAULT NULL, phone VARCHAR(50) DEFAULT NULL, institution VARCHAR(255) DEFAULT NULL, notes LONGTEXT DEFAULT NULL, participant1_first_name VARCHAR(100) DEFAULT NULL, participant1_last_name VARCHAR(100) DEFAULT NULL, participant1_email VARCHAR(180) DEFAULT NULL, participant2_first_name VARCHAR(100) DEFAULT NULL, participant2_last_name VARCHAR(100) DEFAULT NULL, participant2_email VARCHAR(180) DEFAULT NULL, shared_phone VARCHAR(50) DEFAULT NULL, shared_institution VARCHAR(255) DEFAULT NULL, shared_notes LONGTEXT DEFAULT NULL, created_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\', updated_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\', PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');

        $this->addSql('CREATE TABLE `user` (id INT AUTO_INCREMENT NOT NULL, email VARCHAR(180) NOT NULL, roles JSON NOT NULL, password VARCHAR(255) NOT NULL, UNIQUE INDEX UNIQ_IDENTIFIER_EMAIL (email), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('DROP TABLE registered_doctor');
        $this->addSql('DROP TABLE `user`');
    }
}
