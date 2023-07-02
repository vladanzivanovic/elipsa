<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230629210836 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP INDEX idx_1846db702b36786b6de440263f6c5cf9 ON product_translation');
        $this->addSql('CREATE FULLTEXT INDEX IDX_1846DB702B36786B ON product_translation (title)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP INDEX idx_1846db702b36786b ON product_translation');
        $this->addSql('CREATE FULLTEXT INDEX IDX_1846DB702B36786B6DE440263F6C5CF9 ON product_translation (title)');
    }
}
