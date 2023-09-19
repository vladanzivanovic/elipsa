<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230716155313 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $blogHasTags = $this->connection->executeQuery('SELECT * FROM blog_has_tags')->fetchAllAssociative();

        foreach ($blogHasTags as $blogHasTag) {
            $tagTrans = $this->connection->executeQuery(
                sprintf(
                    'SELECT * FROM tag_translation WHERE slug = \'%s\' AND locale = \'rs\'',
                    $blogHasTag['tag_name']
                )
            )->fetchAssociative();

            $this->connection->update(
                'blog_has_tags',
                [
                    'tag_id' => $tagTrans['tag_id'],
                ],
                [
                    'id' => $blogHasTag['id']
                ],
            );
        }
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs

    }
}
