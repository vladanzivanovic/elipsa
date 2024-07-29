<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240714080811 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('UPDATE product_options SET price = price*100');
        $this->addSql('UPDATE product_options SET discount = discount*100 WHERE discount > 0');
        $this->addSql('UPDATE settings SET value = value*100 WHERE slug IN (\'FREE_SHIPPING_STORE\', \'FREE_SHIPPING\', \'SHIPPING_PRICE\', \'FREE_SHIPPING_STORE\')');

    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs

    }
}
