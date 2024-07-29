<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240704182019 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs

        $upHomePages = $this->connection->fetchAllAssociative('SELECT * FROM product_options WHERE show_home_page = \'up\'');

        foreach ($upHomePages as $index => $upHomePage) {
            $index++;

            $this->addSql(
                'UPDATE product_options SET show_home_page = :homePageData WHERE id = :id ',
                [
                    'homePageData' => json_encode(['up' => $index]),
                    'id' => $upHomePage['id'],
                ],
            );
        }

        $downHomePages = $this->connection->fetchAllAssociative('SELECT * FROM product_options WHERE show_home_page = \'down\'');

        foreach ($downHomePages as $index => $downHomePage) {
            $index++;

            $this->addSql(
                'UPDATE product_options SET show_home_page = :homePageData WHERE id = :id ',
                [
                    'homePageData' => json_encode(['down' => $index]),
                    'id' => $downHomePage['id'],
                ],
            );
        }

        $this->addSql('ALTER TABLE product_options CHANGE show_home_page show_home_page JSON DEFAULT NULL COMMENT \'(DC2Type:json)\'');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE product_options CHANGE show_home_page show_home_page VARCHAR(10) DEFAULT NULL');
    }
}
