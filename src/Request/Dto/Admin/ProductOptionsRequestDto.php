<?php

declare(strict_types=1);

namespace App\Request\Dto\Admin;

class ProductOptionsRequestDto
{
    public null|int $price = null;

    public int $discount = 0;

    public bool $isSold;

    public null|array $showHomePage = null;

    public array $sizes;

    public function __construct(array $options)
    {
        $this->price = isset($options['price']) ? $this->convertPriceToInteger($options['price']) : null;
        $this->discount = isset($options['discount']) ? $this->convertPriceToInteger($options['discount']) : 0;
        $this->isSold = isset($options['sold']) ? filter_var($options['sold'], FILTER_VALIDATE_BOOLEAN) : false;
        $this->showHomePage = $options['show_home_page'] ?? null;
        $this->sizes = $options['sizes'];
    }

    private function convertPriceToInteger(string $price): int
    {
        return  (int) ((float) $price * 100);
    }
}
