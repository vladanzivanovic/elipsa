<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240505200450 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE free_shipping_rules DROP FOREIGN KEY FK_481856765EFCCB6B');
        $this->addSql('DROP TABLE free_shipping_rules');
        $this->addSql('DROP TABLE free_shipping');
        $this->addSql('ALTER TABLE banner ADD available_countries LONGTEXT NOT NULL COMMENT \'(DC2Type:simple_array)\'');
        $this->addSql('ALTER TABLE news_letter CHANGE links links JSON DEFAULT NULL COMMENT \'(DC2Type:json)\'');
        $this->addSql('ALTER TABLE notification CHANGE payload payload JSON NOT NULL COMMENT \'(DC2Type:json)\'');
        $this->addSql('ALTER TABLE promotion_option CHANGE configuration configuration JSON NOT NULL COMMENT \'(DC2Type:json)\'');
        $this->addSql('ALTER TABLE shop_order CHANGE transaction_data transaction_data JSON DEFAULT NULL COMMENT \'(DC2Type:json)\', CHANGE tracking_info tracking_info JSON DEFAULT NULL COMMENT \'(DC2Type:json)\'');
        $this->addSql('ALTER TABLE user CHANGE roles roles JSON NOT NULL COMMENT \'(DC2Type:json)\'');
        $this->addSql('ALTER TABLE youtube CHANGE images images JSON NOT NULL COMMENT \'(DC2Type:json)\'');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE free_shipping_rules (id INT AUTO_INCREMENT NOT NULL, free_shipping_id INT NOT NULL, type VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, configuration JSON NOT NULL COMMENT \'(DC2Type:json)\', INDEX IDX_481856765EFCCB6B (free_shipping_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('CREATE TABLE free_shipping (id INT AUTO_INCREMENT NOT NULL, amount INT NOT NULL, status VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('ALTER TABLE free_shipping_rules ADD CONSTRAINT FK_481856765EFCCB6B FOREIGN KEY (free_shipping_id) REFERENCES free_shipping (id)');
        $this->addSql('ALTER TABLE banner DROP available_countries');
        $this->addSql('ALTER TABLE shop_order CHANGE transaction_data transaction_data JSON DEFAULT NULL COMMENT \'(DC2Type:json)\', CHANGE tracking_info tracking_info JSON DEFAULT NULL COMMENT \'(DC2Type:json)\'');
        $this->addSql('ALTER TABLE youtube CHANGE images images JSON NOT NULL COMMENT \'(DC2Type:json)\'');
        $this->addSql('ALTER TABLE promotion_option CHANGE configuration configuration JSON NOT NULL COMMENT \'(DC2Type:json)\'');
        $this->addSql('ALTER TABLE user CHANGE roles roles JSON NOT NULL COMMENT \'(DC2Type:json)\'');
        $this->addSql('ALTER TABLE news_letter CHANGE links links JSON DEFAULT NULL COMMENT \'(DC2Type:json)\'');
        $this->addSql('ALTER TABLE notification CHANGE payload payload JSON NOT NULL COMMENT \'(DC2Type:json)\'');
    }
}
