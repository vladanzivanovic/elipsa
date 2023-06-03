<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230518174047 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE office_contact_translation (id INT AUTO_INCREMENT NOT NULL, office_contact_id INT NOT NULL, title VARCHAR(255) NOT NULL, locale VARCHAR(3) NOT NULL, INDEX IDX_C1CCD798B8B2592B (office_contact_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE office_contact_translation ADD CONSTRAINT FK_C1CCD798B8B2592B FOREIGN KEY (office_contact_id) REFERENCES office_contact (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE office_contact_translation DROP FOREIGN KEY FK_C1CCD798B8B2592B');
        $this->addSql('DROP TABLE office_contact_translation');
    }
}
