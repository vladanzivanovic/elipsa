<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240507224800 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        $products = $this->connection->fetchAllAssociative('SELECT * FROM product');

        foreach ($products as $product) {
            $showHomePage = 0 === $product['show_home_page'] ? null : $product['show_home_page'];

            if (null !== $showHomePage) {
                $showHomePage = $showHomePage === 2 ? 'up' : 'down';
            }

            $this->addSql(
                'INSERT INTO product_options (show_home_page, sold, price, discount, country, product_id)
                        VALUES (:showHomePage, :sold, :price, :discount, :country, :productId)',
                [
                    'showHomePage' => $showHomePage,
                    'sold' => $product['sold'],
                    'price' => $product['price'],
                    'discount' => $product['discount'],
                    'country' => 'rs',
                    'productId' => $product['id']
                ]
            );
        }

    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs

    }
}
