<?php

declare(strict_types=1);

namespace App\Formatter\Site;

use App\Entity\User;
use App\View\OrderView;
use App\View\ProductView;
use App\View\UserView;
use Symfony\Component\Routing\RouterInterface;

final class MyAccountFormatter
{
    private UserView $userView;

    private OrderView $orderView;

    private ProductFormatter $productFormatter;

    public function __construct(
        RouterInterface $router,
        UserView $userView,
        OrderView $orderView,
        ProductFormatter $productFormatter
    ) {
        $this->userView = $userView;
        $this->orderView = $orderView;
        $this->productFormatter = $productFormatter;
    }

    public function formatResponse(User $user, array $data, string $locale): array
    {
        $orders = null;
        $products = [];

        foreach ($user->getShopOrders() as $shopOrder) {
            $orders[] = $this->orderView->view($shopOrder, $locale);
        }

        foreach ($user->getUserWishes() as $userWish) {
            $products[] = $userWish->getProduct();
        }

        $wishes = $this->productFormatter->getProducts($products, $locale, $user);

//        $wishes = array_map(function ($order) {
//            $order['image_link'] = $this->router->generate('app.image_show', ['entity' => 'product', 'name' => $order['image_name'], 'filter' => 'cart_thumb']);
//
//            return $order;
//        }, $data['wishes']);

        return [
            'profile' => $this->userView->view($user),
            'orders' => $orders,
            'wishes' => $wishes,
        ];
    }
}
