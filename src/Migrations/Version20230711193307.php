<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230711193307 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP INDEX IDX_6FBC9426EA750E8 ON tags');
        $this->addSql('ALTER TABLE tags DROP label, DROP locale, DROP slug, DROP main_slug');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE tags ADD label VARCHAR(50) NOT NULL, ADD locale VARCHAR(2) NOT NULL, ADD slug VARCHAR(255) NOT NULL, ADD main_slug VARCHAR(255) NOT NULL');
        $this->addSql('CREATE FULLTEXT INDEX IDX_6FBC9426EA750E8 ON tags (label)');
    }
}
