<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230729085451 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $orderProducts = $this->connection->executeQuery('SELECT * FROM order_product')->fetchAllAssociative();

        foreach ($orderProducts as $orderProduct) {
            $coupon = null;
            $order = $this->connection->executeQuery('SELECT * FROM shop_order WHERE id = '.$orderProduct['order_id_id'])->fetchAssociative();

            if(null !== $order['coupon_id']) {
                $coupon = $this->connection->executeQuery('SELECT * FROM promotion_coupon WHERE id = '.$order['coupon_id'])->fetchAssociative();
            }

            if (null === $coupon) {
                continue;
            }

            $couponPrice = $coupon['discount'] / 100;

            $price = 0 < $orderProduct['discount'] ? $orderProduct['discount'] : $orderProduct['price'];

            $promotionPrice = $price * $couponPrice;

            $this->connection->update(
                'order_product',
                [
                    'promotion_price' => -$promotionPrice,
                ],
                [
                    'id' => $orderProduct['id'],
                ],
            );
        }
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs

    }
}
