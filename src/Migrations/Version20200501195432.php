<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20200501195432 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        $this->abortIf($this->connection->getDriver()->getName() !== 'pdo_mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql("INSERT INTO settings (`name`, `value`, `slug`) VALUES ('Besplatna dostava', 8000, 'FREE_SHIPPING')");
        $this->addSql("INSERT INTO settings (`name`, `value`, `slug`) VALUES ('Cena dostave', 1000, 'SHIPPING_PRICE')");
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs

    }
}
