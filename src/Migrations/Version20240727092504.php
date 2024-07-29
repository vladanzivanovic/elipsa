<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240727092504 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE location ADD available_countries LONGTEXT NOT NULL COMMENT \'(DC2Type:simple_array)\'');
        $this->addSql('UPDATE location SET `available_countries` = \'rs\' WHERE zip_code != \'78000\'');
        $this->addSql('UPDATE location SET `available_countries` = \'ba\' WHERE zip_code = \'78000\'');

    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE location DROP available_countries');
    }
}
