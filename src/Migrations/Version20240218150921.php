<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240218150921 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE shop_order ADD store_id_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE shop_order ADD CONSTRAINT FK_323FC9CA37AC84E FOREIGN KEY (store_id_id) REFERENCES location (id)');
        $this->addSql('CREATE INDEX IDX_323FC9CA37AC84E ON shop_order (store_id_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE shop_order DROP FOREIGN KEY FK_323FC9CA37AC84E');
        $this->addSql('DROP INDEX IDX_323FC9CA37AC84E ON shop_order');
        $this->addSql('ALTER TABLE shop_order DROP store_id_id');
    }
}
