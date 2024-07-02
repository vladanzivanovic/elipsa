<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240620134650 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE image CHANGE device device VARCHAR(25) DEFAULT NULL');

        $this->addSql('UPDATE image SET device = NULL  WHERE device  = \'0\'');
        $this->addSql('UPDATE image SET device = \'desktop\'  WHERE device  = \'1\'');
        $this->addSql('UPDATE image SET device = \'mobile\'  WHERE device  = \'2\'');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE image CHANGE device device SMALLINT NOT NULL');
    }
}
