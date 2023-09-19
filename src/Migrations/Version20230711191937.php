<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230711191937 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $tags = $this->connection->executeQuery('SELECT * FROM tags')->fetchAllAssociative();

        foreach ($tags as $tag) {
            $mainTag = $this->connection->executeQuery(
                sprintf(
                    'SELECT * FROM tags WHERE main_slug = \'%s\' AND locale = \'rs\'',
                    $tag['main_slug']
                )
            )->fetchAssociative();

            $this->connection->insert(
                'tag_translation',
                [
                    'title' => $tag['label'],
                    'slug' => $tag['slug'],
                    'locale' => $tag['locale'],
                    'tag_id' => $mainTag['id'],
                ]
            );
        }
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs

    }
}
