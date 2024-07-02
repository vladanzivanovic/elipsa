<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240508181050 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE product_has_sizes DROP FOREIGN KEY FK_23B7215A4584665A');
        $this->addSql('DROP INDEX IDX_23B7215A4584665A ON product_has_sizes');
        $this->addSql('ALTER TABLE product_has_sizes DROP product_id');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE product_has_sizes ADD product_id INT NOT NULL');
        $this->addSql('ALTER TABLE product_has_sizes ADD CONSTRAINT FK_23B7215A4584665A FOREIGN KEY (product_id) REFERENCES product (id)');
        $this->addSql('CREATE INDEX IDX_23B7215A4584665A ON product_has_sizes (product_id)');
    }
}
