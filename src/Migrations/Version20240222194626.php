<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240222194626 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE free_shipping_rules (id INT AUTO_INCREMENT NOT NULL, free_shipping_id INT NOT NULL, type VARCHAR(255) NOT NULL, configuration LONGTEXT NOT NULL COMMENT \'(DC2Type:json)\', INDEX IDX_481856765EFCCB6B (free_shipping_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE free_shipping_rules ADD CONSTRAINT FK_481856765EFCCB6B FOREIGN KEY (free_shipping_id) REFERENCES free_shipping (id)');
        $this->addSql('ALTER TABLE free_shipping DROP rules');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE free_shipping_rules DROP FOREIGN KEY FK_481856765EFCCB6B');
        $this->addSql('DROP TABLE free_shipping_rules');
        $this->addSql('ALTER TABLE free_shipping ADD rules LONGTEXT NOT NULL COMMENT \'(DC2Type:json)\'');
    }
}
