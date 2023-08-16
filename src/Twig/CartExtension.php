<?php

declare(strict_types=1);

namespace App\Twig;

use App\Collector\CartPageCollector;
use App\Formatter\Site\CartPageFormatter;
use App\Formatter\Site\OrderEditResponseFormatter;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

final class CartExtension extends AbstractExtension
{
    /**
     * @var CartPageFormatter
     */
    private $pageFormatter;

    /**
     * @var CartPageCollector
     */
    private $pageCollector;

    private OrderEditResponseFormatter $responseFormatter;

    /**
     * @param CartPageCollector $pageCollector
     * @param CartPageFormatter $pageFormatter
     */
    public function __construct(
        CartPageCollector $pageCollector,
        CartPageFormatter $pageFormatter,
        OrderEditResponseFormatter $responseFormatter
    ) {
        $this->pageFormatter = $pageFormatter;
        $this->pageCollector = $pageCollector;
        $this->responseFormatter = $responseFormatter;
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
     * @param string  $locale
     *
     * @return array
     */
    public function getCart(string $locale): array
    {
        $orderData = $this->pageCollector->collect($locale);

        if (null == $orderData['order']) {
            return $orderData;
        }

        return $this->responseFormatter->formatResponse(
            $order,
            $locale
        );

        return $this->pageFormatter->formatResponse($orderData);
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'cart_extension';
    }
}
