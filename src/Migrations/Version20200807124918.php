<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20200807124918 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        $this->abortIf($this->connection->getDriver()->getName() !== 'pdo_mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql("INSERT INTO settings (`name`, `value`, `slug`) VALUES ('Pun naziv firme', 'JOVAN PANTOVIC PR ZANATSKO TRGOVINSKO I KOMISIONA RADNJA', 'FULL_COMPANY_NAME')");

        $this->addSql("INSERT INTO settings (`name`, `value`, `slug`) VALUES ('Delatnost', 'ZANATSKO TRGOVINSKO I KOMISIONA RADNJA', 'COMPANY_ACTIVITY')");

        $this->addSql("INSERT INTO settings (`name`, `value`, `slug`) VALUES ('Šifra delatnosti', '6201', 'COMPANY_CODE')");

        $this->addSql("INSERT INTO settings (`name`, `value`, `slug`) VALUES ('Matični broj', '6201', 'COMPANY_ID')");

    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs

    }
}
