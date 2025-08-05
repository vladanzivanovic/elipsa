<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250704144353 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $productHasTags = $this->connection->fetchAllAssociative('SELECT * FROM product_has_tags');

        foreach ($productHasTags as $productHasTag) {
            $this->addSql(
                'INSERT INTO product_has_tags (product_id, tag_id, locale) VALUES(:product_id, :tag_id, :locale)',
                [
                    'product_id' => $productHasTag['product_id'],
                    'tag_id' => $productHasTag['tag_id'],
                    'locale' => 'ba'
                ]
            );
        }
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs

    }
}
