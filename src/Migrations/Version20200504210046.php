<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20200504210046 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        $this->abortIf($this->connection->getDriver()->getName() !== 'pdo_mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql("INSERT INTO settings (`name`, `value`, `slug`) VALUES ('Glavna email adresa', 'office@elipsa.rs', 'MAIN_EMAIL')");
        $this->addSql("INSERT INTO settings (`name`, `value`, `slug`) VALUES ('Broj telefona', '+38111/222-333', 'TELEPHONE')");
        $this->addSql("INSERT INTO settings (`name`, `value`, `slug`) VALUES ('Broj mobilnog telefona', '+38160/222-333', 'MOBILE_PHONE')");
        $this->addSql("INSERT INTO settings (`name`, `value`, `slug`) VALUES ('Ulica i broj', 'Cede Vasovica 47/20', 'STREET')");
        $this->addSql("INSERT INTO settings (`name`, `value`, `slug`) VALUES ('Mesto', 'Beograd', 'CITY')");
        $this->addSql("INSERT INTO settings (`name`, `value`, `slug`) VALUES ('Poštanski broj', '11000', 'ZIP_CODE')");
        $this->addSql("INSERT INTO settings (`name`, `value`, `slug`) VALUES ('Žiro račun', '850-111-46', 'ACCOUNT_NUMBER')");
        $this->addSql("INSERT INTO settings (`name`, `value`, `slug`) VALUES ('PIB', '1110000111', 'PIB')");
        $this->addSql("INSERT INTO settings (`name`, `value`, `slug`) VALUES ('Naziv firme', 'Elipsa', 'SITE_NAME')");
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs

    }
}
