<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240507224636 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE product_options (id INT AUTO_INCREMENT NOT NULL, product_id INT NOT NULL, show_home_page VARCHAR(10) DEFAULT NULL, sold TINYINT(1) NOT NULL, price INT NOT NULL, discount INT DEFAULT NULL, country VARCHAR(255) NOT NULL, INDEX IDX_1ECE1374584665A (product_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE product_options ADD CONSTRAINT FK_1ECE1374584665A FOREIGN KEY (product_id) REFERENCES product (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE product_options DROP FOREIGN KEY FK_1ECE1374584665A');
        $this->addSql('DROP TABLE product_options');
    }
}
