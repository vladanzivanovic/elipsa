<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240525111745 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE career_description ADD available_countries LONGTEXT NOT NULL COMMENT \'(DC2Type:simple_array)\', CHANGE status status VARCHAR(20) NOT NULL');

        $this->addSql('UPDATE career_description SET status = \'pending\' WHERE status = \'1\'');
        $this->addSql('UPDATE career_description SET status = \'active\' WHERE status = \'2\'');
        $this->addSql('UPDATE career_description SET status = \'archived\' WHERE status = \'3\'');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE career_description DROP available_countries, CHANGE status status SMALLINT NOT NULL');
    }
}
