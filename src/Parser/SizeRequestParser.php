<?php

declare(strict_types=1);

namespace App\Parser;

use App\Entity\ProductSize;
use App\Entity\Resources\StatusInterface;
use App\Request\Dto\Admin\SizeEditRequestDto;
use Symfony\Component\HttpFoundation\ParameterBag;

final class SizeRequestParser
{
    /**
     * @param ProductSize|null $productSize
     *
     */
    public function parse(SizeEditRequestDto $sizeEditRequestDto, ProductSize $productSize = null): ProductSize
    {
        if (!$productSize instanceof ProductSize) {
            $productSize = new ProductSize();
            $productSize->setStatus(StatusInterface::STATUS_ACTIVE);
        }

        $productSize->setSize($sizeEditRequestDto->size);

        return $productSize;
    }
}
