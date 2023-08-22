<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230411204006 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('DROP TABLE product_has_color');
        $this->addSql('ALTER TABLE address CHANGE user_id user_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE career CHANGE cv_id cv_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE category CHANGE parent_id parent_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE collaborator CHANGE presentation_id presentation_id INT DEFAULT NULL, CHANGE plan_id plan_id INT DEFAULT NULL, CHANGE phone phone VARCHAR(20) DEFAULT NULL, CHANGE website website VARCHAR(255) DEFAULT NULL, CHANGE country country VARCHAR(100) DEFAULT NULL, CHANGE city city VARCHAR(100) DEFAULT NULL, CHANGE shopping_mall shopping_mall VARCHAR(255) DEFAULT NULL, CHANGE space_size space_size INT DEFAULT NULL, CHANGE number_of_floors number_of_floors INT DEFAULT NULL, CHANGE address address VARCHAR(255) DEFAULT NULL, CHANGE zip_code zip_code INT DEFAULT NULL');
        $this->addSql('ALTER TABLE image CHANGE parent_image parent_image VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE location CHANGE email email VARCHAR(255) DEFAULT NULL, CHANGE telephone telephone VARCHAR(15) DEFAULT NULL');
        $this->addSql('ALTER TABLE loyalty CHANGE mobile_phone mobile_phone VARCHAR(15) DEFAULT NULL, CHANGE birth_date birth_date DATETIME DEFAULT NULL, CHANGE occupation occupation VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE news_letter CHANGE user_id user_id INT DEFAULT NULL, CHANGE last_error last_error VARCHAR(255) DEFAULT NULL, CHANGE links links JSON DEFAULT NULL, CHANGE chimp_id chimp_id VARCHAR(255) DEFAULT NULL, CHANGE status status VARCHAR(100) DEFAULT NULL');
        $this->addSql('ALTER TABLE order_product CHANGE discount discount INT DEFAULT NULL');
        $this->addSql('ALTER TABLE product CHANGE discount discount INT DEFAULT NULL, CHANGE badge badge SMALLINT DEFAULT NULL');
        $this->addSql('ALTER TABLE product_has_sizes CHANGE size_id size_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE settings CHANGE locale locale VARCHAR(2) DEFAULT NULL');
        $this->addSql('ALTER TABLE shop_order CHANGE billing_address_id billing_address_id INT DEFAULT NULL, CHANGE shipping_address_id shipping_address_id INT DEFAULT NULL, CHANGE user_id user_id INT DEFAULT NULL, CHANGE coupon_id coupon_id INT DEFAULT NULL, CHANGE completed_at completed_at DATETIME DEFAULT NULL, CHANGE payment_type payment_type SMALLINT DEFAULT NULL, CHANGE note note VARCHAR(255) DEFAULT NULL, CHANGE transaction_data transaction_data JSON DEFAULT NULL');
        $this->addSql('ALTER TABLE slider DROP text_position');
        $this->addSql('DROP INDEX UNIQ_6FBC9426782EA9004180C698 ON tags');
        $this->addSql('ALTER TABLE user CHANGE password password VARCHAR(255) DEFAULT NULL, CHANGE reset_token reset_token VARCHAR(255) DEFAULT NULL, CHANGE reset_request_at reset_request_at DATETIME DEFAULT NULL');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE product_has_color (id INT AUTO_INCREMENT NOT NULL, product_id INT NOT NULL, color VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, INDEX IDX_F37FEEDA4584665A (product_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('ALTER TABLE product_has_color ADD CONSTRAINT FK_F37FEEDA4584665A FOREIGN KEY (product_id) REFERENCES product (id)');
        $this->addSql('ALTER TABLE address CHANGE user_id user_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE career CHANGE cv_id cv_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE category CHANGE parent_id parent_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE collaborator CHANGE presentation_id presentation_id INT DEFAULT NULL, CHANGE plan_id plan_id INT DEFAULT NULL, CHANGE phone phone VARCHAR(20) CHARACTER SET utf8mb4 DEFAULT \'NULL\' COLLATE `utf8mb4_unicode_ci`, CHANGE website website VARCHAR(255) CHARACTER SET utf8mb4 DEFAULT \'NULL\' COLLATE `utf8mb4_unicode_ci`, CHANGE country country VARCHAR(100) CHARACTER SET utf8mb4 DEFAULT \'NULL\' COLLATE `utf8mb4_unicode_ci`, CHANGE city city VARCHAR(100) CHARACTER SET utf8mb4 DEFAULT \'NULL\' COLLATE `utf8mb4_unicode_ci`, CHANGE shopping_mall shopping_mall VARCHAR(255) CHARACTER SET utf8mb4 DEFAULT \'NULL\' COLLATE `utf8mb4_unicode_ci`, CHANGE space_size space_size INT DEFAULT NULL, CHANGE number_of_floors number_of_floors INT DEFAULT NULL, CHANGE address address VARCHAR(255) CHARACTER SET utf8mb4 DEFAULT \'NULL\' COLLATE `utf8mb4_unicode_ci`, CHANGE zip_code zip_code INT DEFAULT NULL');
        $this->addSql('ALTER TABLE image CHANGE parent_image parent_image VARCHAR(255) CHARACTER SET utf8mb4 DEFAULT \'NULL\' COLLATE `utf8mb4_unicode_ci`');
        $this->addSql('ALTER TABLE location CHANGE email email VARCHAR(255) CHARACTER SET utf8mb4 DEFAULT \'NULL\' COLLATE `utf8mb4_unicode_ci`, CHANGE telephone telephone VARCHAR(15) CHARACTER SET utf8mb4 DEFAULT \'NULL\' COLLATE `utf8mb4_unicode_ci`');
        $this->addSql('ALTER TABLE loyalty CHANGE mobile_phone mobile_phone VARCHAR(15) CHARACTER SET utf8mb4 DEFAULT \'NULL\' COLLATE `utf8mb4_unicode_ci`, CHANGE birth_date birth_date DATETIME DEFAULT \'NULL\', CHANGE occupation occupation VARCHAR(255) CHARACTER SET utf8mb4 DEFAULT \'NULL\' COLLATE `utf8mb4_unicode_ci`');
        $this->addSql('ALTER TABLE news_letter CHANGE user_id user_id INT DEFAULT NULL, CHANGE last_error last_error VARCHAR(255) CHARACTER SET utf8mb4 DEFAULT \'NULL\' COLLATE `utf8mb4_unicode_ci`, CHANGE links links JSON CHARACTER SET utf8mb4 DEFAULT \'NULL\' COLLATE `utf8mb4_unicode_ci`, CHANGE chimp_id chimp_id VARCHAR(255) CHARACTER SET utf8mb4 DEFAULT \'NULL\' COLLATE `utf8mb4_unicode_ci`, CHANGE status status VARCHAR(100) CHARACTER SET utf8mb4 DEFAULT \'NULL\' COLLATE `utf8mb4_unicode_ci`');
        $this->addSql('ALTER TABLE order_product CHANGE discount discount INT DEFAULT NULL');
        $this->addSql('ALTER TABLE product CHANGE discount discount INT DEFAULT NULL, CHANGE badge badge SMALLINT DEFAULT NULL');
        $this->addSql('ALTER TABLE product_has_sizes CHANGE size_id size_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE settings CHANGE locale locale VARCHAR(2) CHARACTER SET utf8mb4 DEFAULT \'NULL\' COLLATE `utf8mb4_unicode_ci`');
        $this->addSql('ALTER TABLE shop_order CHANGE billing_address_id billing_address_id INT DEFAULT NULL, CHANGE shipping_address_id shipping_address_id INT DEFAULT NULL, CHANGE user_id user_id INT DEFAULT NULL, CHANGE coupon_id coupon_id INT DEFAULT NULL, CHANGE completed_at completed_at DATETIME DEFAULT \'NULL\', CHANGE payment_type payment_type SMALLINT DEFAULT NULL, CHANGE note note VARCHAR(255) CHARACTER SET utf8mb4 DEFAULT \'NULL\' COLLATE `utf8mb4_unicode_ci`, CHANGE transaction_data transaction_data JSON CHARACTER SET utf8mb4 DEFAULT \'NULL\' COLLATE `utf8mb4_unicode_ci`');
        $this->addSql('ALTER TABLE slider ADD text_position SMALLINT NOT NULL');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_6FBC9426782EA9004180C698 ON tags (main_slug, locale)');
        $this->addSql('ALTER TABLE user CHANGE password password VARCHAR(255) CHARACTER SET utf8mb4 DEFAULT \'NULL\' COLLATE `utf8mb4_unicode_ci`, CHANGE reset_token reset_token VARCHAR(255) CHARACTER SET utf8mb4 DEFAULT \'NULL\' COLLATE `utf8mb4_unicode_ci`, CHANGE reset_request_at reset_request_at DATETIME DEFAULT \'NULL\'');
    }
}
