<?php

namespace App\Formatter\Site;

trait FormatterTrait
{
    /**
     * @param array $productColors
     *
     * @return array
     */
    private function formatColors(array $productColors): array
    {
        $formattedProduct = [];

        foreach ($productColors as $productColor) {
            $formattedProduct[$productColor['productId']][] = $productColor['hex'];
        }

        return $formattedProduct;
    }

    /**
     * @param array $productSizes
     *
     * @return array
     */
    private function formatSizes(array $productSizes): array
    {
        $formattedProduct = [];

        foreach ($productSizes as $productSize) {
            $formattedProduct[$productSize['productId']][] = $productSize['size'];
        }

        return $formattedProduct;
    }

    /**
     * @param array $productTags
     *
     * @return array
     */
    private function formatTags(array $productTags): array
    {
        $formattedProduct = [];

        foreach ($productTags as $productTag) {
            $formattedProduct[$productTag['productId']][] = $productTag;
        }

        return $formattedProduct;
    }
}