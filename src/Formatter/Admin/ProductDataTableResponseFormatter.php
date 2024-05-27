<?php

declare(strict_types=1);

namespace App\Formatter\Admin;

use App\Entity\Product;
use App\Entity\ProductOptions;
use App\Helper\ConstantsHelper;
use App\Model\DataTableModel;
use App\Repository\ProductOptionsRepository;
use App\Repository\ProductSizeRepository;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

final class ProductDataTableResponseFormatter
{
    use DataTableResponseTrait;

    public function __construct(
        private readonly TranslatorInterface $translator,
        private readonly ProductSizeRepository $sizeRepository,
        private readonly ProductOptionsRepository $productOptionsRepository,
        private readonly RouterInterface $router,
        private readonly array $countries,
    ) {}

    public function formatResponse(DataTableModel $tableModel, array $data, int $total): array
    {
        $data = array_map(function ($product) {
            $statusText = ConstantsHelper::getConstantName((string) $product['status'], 'STATUS', Product::class);
            $product['status_text'] = $this->translator->trans($statusText);
            $product['link'] = $this->router->generate('site.product_page', ['slug' => $product['slug']]);

            $options = $this->productOptionsRepository->findBy(['product' => $product['id']]);

            $product['sold'] = [];
            $product['show_home_page'] = [];
            $product['prices'] = [];
            $product['discounts'] = [];

            foreach ($options as $options) {
                $this->setSold($options, $product['sold']);
                $this->setHomePagePosition($options, $product['show_home_page']);
                $this->setPrices($options, $product['prices']);
                $this->setDiscounts($options, $product['discounts']);
            }

            return $product;
        }, $data);

        return $this->response($tableModel, $data, $total);

    }

    private function setSold(ProductOptions $options, array &$sold): void
    {
        $sold[$options->getCountry()] = $options->isSold();
    }

    private function setHomePagePosition(ProductOptions $options, array &$showHomePage): void
    {
        if (null !== $options->getShowHomePage()) {
            $showHomePage[$options->getCountry()] = ConstantsHelper::getConstantName($options->getShowHomePage(), 'HOME_PAGE', ProductOptions::class);;
        }
    }

    private function setPrices(ProductOptions $options, array &$prices): void
    {
        $prices[$options->getCountry()] = $options->getPrice();
    }

    private function setDiscounts(ProductOptions $options, array &$discounts): void
    {
        $discounts[$options->getCountry()] = $options->getDiscount();
    }
}
