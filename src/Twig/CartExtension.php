<?php

declare(strict_types=1);

namespace App\Twig;

use App\Repository\CategoryRepository;
use App\Repository\OrderProductRepository;
use App\Repository\ShopOrderRepository;
use DateTime;
use Exception;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\Routing\RouterInterface;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

final class CartExtension extends AbstractExtension
{
    /**
     * @var OrderProductRepository
     */
    private $orderProductRepository;

    /**
     * @var ShopOrderRepository
     */
    private $orderRepository;
    /**
     * @var RouterInterface
     */
    private $router;

    /**
     * NavigationMenuExtension constructor.
     *
     * @param ShopOrderRepository    $orderRepository
     * @param OrderProductRepository $orderProductRepository
     * @param RouterInterface        $router
     */
    public function __construct(
        ShopOrderRepository $orderRepository,
        OrderProductRepository $orderProductRepository,
        RouterInterface $router
    ) {
        $this->orderProductRepository = $orderProductRepository;
        $this->orderRepository = $orderRepository;
        $this->router = $router;
    }

    /**
     * @return array
     */
    public function getFunctions()
    {
        return [
            new TwigFunction('cart_list', [$this, 'getCart']),
        ];
    }

    /**
     * @param string $locale
     *
     * @param int    $orderId
     *
     * @return array
     */
    public function getCart(string $locale, Session $session): array
    {
        if (!$session->has('order')) {
            return ['products' => [], 'total' => 0];
        }
        $orderId = $session->get('order');
        $order = $this->orderRepository->find($orderId);
        $orderProducts = $this->orderProductRepository->getByOrder($order, $locale);

        $productArray = [];
        $total = 0;

        foreach ($orderProducts as $orderProduct) {
            $productArray[] = [
                'id'        => $orderProduct['id'],
                'name'      => $orderProduct['title'],
                'slug'      => $orderProduct['slug'],
                'image_link'        => $this->router->generate('app.image_show', ['name' => $orderProduct['image_name'], 'filter' => 'cart_thumb']),
                'price'     => $orderProduct['price'],
                'discount'  => $orderProduct['discount'],
                'quantity'  => $orderProduct['quantity'],
            ];

            $total += $orderProduct['discount'] > 0 ? $orderProduct['discount']*$orderProduct['quantity'] : $orderProduct['price']*$orderProduct['quantity'];
        }


        return ['products' => $productArray, 'total' => $total];
    }

    private function formatMegaMenu(array $categories, int $level, int $maxLevel)
    {
        $formattedMenu = [];

        foreach ($categories as $category) {
            $childrenCategories = array_filter($categories, function ($cat) use ($category) {
                return $cat['parent_id'] === $category['id'];
            });

            if (count($childrenCategories) > 0) {
                $category['children'] = $childrenCategories;
            }

            $formattedMenu[] = $category;
        }

        if ($level <= $maxLevel) {
            return self::formatMegaMenu($formattedMenu, $level + 1, $maxLevel);
        }

        return $formattedMenu;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'cart_extension';
    }
}
