<?php

declare(strict_types=1);

namespace App\Formatter\Site;

use App\Entity\ShopOrder;
use App\Repository\SettingsRepository;
use Symfony\Component\Routing\RouterInterface;

final class MyAccountFormatter
{
    /**
     * @var RouterInterface
     */
    private $router;
    /**
     * @var SettingsRepository
     */
    private $settingsRepository;

    /**
     * CartPageFormatter constructor.
     *
     * @param RouterInterface    $router
     * @param SettingsRepository $settingsRepository
     */
    public function __construct(
        RouterInterface $router,
        SettingsRepository $settingsRepository
    ) {
        $this->router = $router;
        $this->settingsRepository = $settingsRepository;
    }

    public function formatResponse(array $data): array
    {
        $data['orders'] = array_map(function ($order) {
            $order['image_link'] = $this->router->generate('app.image_show', ['entity' => 'product', 'name' => $order['image_name'], 'filter' => 'cart_thumb']);

            return $order;
        }, $data['orders']);

        $data['wishes'] = array_map(function ($order) {
            $order['image_link'] = $this->router->generate('app.image_show', ['entity' => 'product', 'name' => $order['image_name'], 'filter' => 'cart_thumb']);

            return $order;
        }, $data['wishes']);

        return $data;
    }
}