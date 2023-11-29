<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230711201650 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE product_has_tags ADD tag_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE product_has_tags ADD CONSTRAINT FK_F95025EEBAD26311 FOREIGN KEY (tag_id) REFERENCES tags (id)');
        $this->addSql('CREATE INDEX IDX_F95025EEBAD26311 ON product_has_tags (tag_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE product_has_tags DROP FOREIGN KEY FK_F95025EEBAD26311');
        $this->addSql('DROP INDEX IDX_F95025EEBAD26311 ON product_has_tags');
        $this->addSql('ALTER TABLE product_has_tags DROP tag_id');
    }
}
