<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230603062350 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE settings ADD input_type VARCHAR(15) NOT NULL');

        $this->addSql('UPDATE settings SET input_type = \'number\' WHERE slug IN (\'FREE_SHIPPING\', \'SHIPPING_PRICE\', \'ZIP_CODE\', \'COMPANY_CODE\', \'COMPANY_ID\', \'PIB\')');
        $this->addSql('UPDATE settings SET input_type = \'email\' WHERE slug = \'MAIN_EMAIL\'');
        $this->addSql('UPDATE settings SET input_type = \'tel\' WHERE slug IN (\'TELEPHONE\', \'MOBILE_PHONE\')');
        $this->addSql('UPDATE settings SET input_type = \'text\' WHERE slug IN (\'STREET\', \'CITY\', \'ACCOUNT_NUMBER\', \'SITE_NAME\', \'FULL_COMPANY_NAME\', \'COMPANY_ACTIVITY\')');

        $this->addSql('INSERT INTO settings (name, value, slug, input_type, locale) VALUES (\'Footer tekst\', \'\', \'FOOTER_BOTTOM_TEXT\', \'textarea\', \'rs\')');
        $this->addSql('INSERT INTO settings (name, value, slug, input_type, locale) VALUES (\'Footer tekst\', \'\', \'FOOTER_BOTTOM_TEXT\', \'textarea\', \'en\')');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE settings DROP input_type');
    }
}
