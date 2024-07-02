<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240527191156 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql(
            'UPDATE settings set country = :country WHERE slug = :slug',
            [
                'country' => 'rs',
                'slug' => 'FREE_SHIPPING',
            ]
        );
        $this->addSql(
            'UPDATE settings set country = :country WHERE slug = :slug',
            [
                'country' => 'rs',
                'slug' => 'SHIPPING_PRICE',
            ]
        );
        $this->addSql(
            'UPDATE settings set country = :country WHERE slug = :slug',
            [
                'country' => 'rs',
                'slug' => 'FREE_SHIPPING_STORE',
            ]
        );

        $this->addSql(
            'INSERT INTO settings (slug, name, value, input_type, country) VALUES (:slug, :name, :value, :input_type, :country)',
            [
                'slug' => 'FREE_SHIPPING',
                'name' => 'Besplatna dostava',
                'value' => '0',
                'input_type' => 'number',
                'country' => 'ba',
            ]
        );

        $this->addSql(
            'INSERT INTO settings (slug, name, value, input_type, country) VALUES (:slug, :name, :value, :input_type, :country)',
            [
                'slug' => 'SHIPPING_PRICE',
                'name' => 'Cena dostave',
                'value' => '0',
                'input_type' => 'number',
                'country' => 'ba',
            ]
        );

        $this->addSql(
            'INSERT INTO settings (slug, name, value, input_type, country) VALUES (:slug, :name, :value, :input_type, :country)',
            [
                'slug' => 'FREE_SHIPPING_STORE',
                'name' => 'Besplatna dostava u prodavnici',
                'value' => '0',
                'input_type' => 'number',
                'country' => 'ba',
            ]
        );
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs

    }
}
