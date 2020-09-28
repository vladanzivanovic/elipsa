<?php

declare(strict_types=1);

namespace App\Collector;

use App\Repository\OrderProductRepository;
use App\Repository\ShopOrderRepository;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

final class CartPageCollector
{
    /**
     * @var SessionInterface
     */
    private $session;
    /**
     * @var OrderProductRepository
     */
    private $orderProductRepository;
    /**
     * @var ShopOrderRepository
     */
    private $orderRepository;

    /**
     * CartPageCollector constructor.
     *
     * @param SessionInterface       $session
     * @param OrderProductRepository $orderProductRepository
     * @param ShopOrderRepository    $orderRepository
     */
    public function __construct(
        SessionInterface $session,
        OrderProductRepository $orderProductRepository,
        ShopOrderRepository $orderRepository
    ) {
        $this->session = $session;
        $this->orderProductRepository = $orderProductRepository;
        $this->orderRepository = $orderRepository;
    }

    /**
     * @param string $locale
     *
     * @return array
     */
    public function collect(string $locale): array
    {
        if (!$this->session->has('order')) {
            return ['products' => [], 'total' => 0, 'order' => null];
        }

        $orderToken = $this->session->get('order');
        $order = $this->orderRepository->getByToken($orderToken);

        $products = $this->orderProductRepository->getByOrder($order, $locale);

        return ['products' => $products, 'order' => $order];
    }
}