<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use App\Entity\Description;
use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230603071731 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        $aboutUsRS = $this->connection->executeQuery('SELECT * FROM settings WHERE slug = \'ABOUT_US\' and locale = \'rs\'')->fetchAssociative();

        $aboutUsEn = $this->connection->executeQuery('SELECT * FROM settings WHERE slug = \'ABOUT_US\' and locale = \'en\'')->fetchAssociative();

        $this->connection->insert(
            'description',
            [
                'description' => $aboutUsRS['value'],
                'type' => 'ABOUT_US',
                'locale' => 'rs'
            ]
        );

        $this->connection->insert(
            'description',
            [
                'description' => $aboutUsEn['value'],
                'type' => 'ABOUT_US',
                'locale' => 'en'
            ]
        );
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs

    }
}
