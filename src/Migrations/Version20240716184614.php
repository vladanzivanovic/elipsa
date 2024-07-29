<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use App\Entity\ShopOrder;
use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240716184614 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE shop_order ADD shipping_price INT NOT NULL');

        $freeShipping = [
            'rs' => $this->connection->fetchAssociative('SELECT * FROM settings WHERE slug = \'FREE_SHIPPING\' AND country = \'rs\''),
            'ba' => $this->connection->fetchAssociative('SELECT * FROM settings WHERE slug = \'FREE_SHIPPING\' AND country = \'ba\''),
            'rs_store' => $this->connection->fetchAssociative('SELECT * FROM settings WHERE slug = \'FREE_SHIPPING_STORE\' AND country = \'rs\''),
            'ba_store' => $this->connection->fetchAssociative('SELECT * FROM settings WHERE slug = \'FREE_SHIPPING_STORE\' AND country = \'ba\''),
        ];

        $shippingPrices = [
            'rs' => $this->connection->fetchAssociative('SELECT * FROM settings WHERE slug = \'SHIPPING_PRICE\' AND country = \'rs\''),
            'ba' => $this->connection->fetchAssociative('SELECT * FROM settings WHERE slug = \'SHIPPING_PRICE\' AND country = \'ba\''),
        ];

        $orders = $this->connection->fetchAllAssociative(
            'SELECT * FROM shop_order WHERE completed_at IS NOT NULL',
        );

        foreach ($orders as $order) {
            $freeShippingPrice = $freeShipping[$order['country']]['value'];

            if (ShopOrder::SHIPPING_TYPE_IN_STORE === $order['shipping_type']) {
                $freeShippingPrice = $freeShipping[$order['country'].'_store']['value'];
            }

            $orderItems = $this->connection->fetchAllAssociative
            (
                'SELECT * FROM order_product WHERE order_id_id = :orderId',
                ['orderId' => $order['id']],
            );

            $total = 0;

            foreach ($orderItems as $orderItem) {
                $total += $orderItem['discount'] > 0 ? $orderItem['discount'] : $orderItem['price'];
            }

            $shippingPrice = $total > $freeShippingPrice ? $shippingPrices[$order['country']]['value'] : 0;

            $this->addSql(
                'UPDATE shop_order SET shipping_price = :shippingPrice WHERE id = :id',
                [
                    'shippingPrice' => $shippingPrice,
                    'id' => $order['id'],
                ]
            );
        }
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE shop_order DROP shipping_price');
    }
}
