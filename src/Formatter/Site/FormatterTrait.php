<?php

namespace App\Formatter\Site;

trait FormatterTrait
{
    
    private function formatColors(array $productColors): array
    {
        $formattedProduct = [];

        foreach ($productColors as $productColor) {
            $formattedProduct[$productColor['productId']][] = $productColor['hex'];
        }

        return $formattedProduct;
    }

    
    private function formatSizes(array $productSizes): array
    {
        $formattedProduct = [];

        foreach ($productSizes as $productSize) {
            $formattedProduct[$productSize['productId']][] = $productSize['size'];
        }

        return $formattedProduct;
    }

    private function formatTags(array $tags): array
    {
        $tagList = [];

        foreach ($tags as $tag) {
            $tagList[] = $this->tagView->view($tag);
        }

        return $tagList;
    }
}
