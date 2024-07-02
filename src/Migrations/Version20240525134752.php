<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240525134752 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE slider_text ADD available_countries LONGTEXT NOT NULL COMMENT \'(DC2Type:simple_array)\', ADD status VARCHAR(255) NOT NULL');

        $this->addSql(
            'UPDATE slider_text SET available_countries = :countries',
            ['countries' => 'rs'],
        );

        $this->addSql(
            'UPDATE slider_text SET status = :status WHERE is_active = :isActive',
            ['status' => 'active', 'isActive' => 1],
        );

        $this->addSql(
            'UPDATE slider_text SET status = :status WHERE is_active = :isActive',
            ['status' => 'pending', 'isActive' => 0],
        );
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE slider_text DROP available_countries, DROP status');
    }
}
