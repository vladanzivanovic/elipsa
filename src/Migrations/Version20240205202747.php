<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240205202747 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('UPDATE shop_order SET `card_status` = \'Void\' WHERE `status` = \'6\'');
        $this->addSql('UPDATE shop_order SET `card_status` = \'Refund\' WHERE `status` = \'5\'');
        $this->addSql('UPDATE shop_order SET `card_status` = \'PostAuth\' WHERE `status` = \'4\'');
        $this->addSql('UPDATE shop_order SET `card_status` = \'Failed\' WHERE `status` = \'3\'');

        $this->addSql('UPDATE shop_order SET `shipping_type` = \'on_delivery\'');

        $this->addSql('UPDATE shop_order SET `status` = \'canceled\' WHERE `status` = \'6\' or `status` = \'5\'');
        $this->addSql('UPDATE shop_order SET `status` = \'failed\' WHERE `status` = \'3\'');
        $this->addSql('UPDATE shop_order SET `status` = \'pending\' WHERE `status` = \'4\'');
        $this->addSql('UPDATE shop_order SET `status` = \'completed\' WHERE `status` = \'2\'');
        $this->addSql('UPDATE shop_order SET `status` = \'completed\' WHERE `status` = \'1\' and completed_at IS NOT NULL');
        $this->addSql('UPDATE shop_order SET `status` = \'pending\' WHERE `status` = \'1\' and completed_at IS NULL');

        $this->addSql('UPDATE shop_order SET payment_type = \'on_delivery\' WHERE payment_type = \'1\'');
        $this->addSql('UPDATE shop_order SET payment_type = \'credit_card\' WHERE payment_type = \'2\'');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs

    }
}
