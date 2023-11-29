<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20231124122138 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $noSize = $this->connection->executeQuery('SELECT * FROM product_size WHERE slug = "no-sizes" ')->fetchAssociative();

        $products = $this->connection->executeQuery("SELECT phs.* FROM product_has_sizes as phs inner join product_has_categories as phc on phs.product_id = phc.product_id and phc.category_id = 45 group by phs.product_id")->fetchAllAssociative();

        foreach ($products as $product) {
            $this->connection->update(
                'product_has_sizes',
                [
                    'size_id' => $noSize['id']
                ],
                [
                    'id' => $product['id']
                ]
            );
        }
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs

    }
}
