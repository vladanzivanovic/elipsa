<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240508175923 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $productOptions = $this->connection->fetchAllAssociative('SELECT * FROM product_options');

        foreach ($productOptions as $productOption) {
            $this->addSql(
                'UPDATE product_has_sizes SET product_option_id = :productOption WHERE product_id = :productId',
                [
                    'productOption' => $productOption['id'],
                    'productId' => $productOption['product_id']
                ]
            );
        }
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs

    }
}
