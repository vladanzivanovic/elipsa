<?php

declare(strict_types=1);

namespace App\Parser;

use App\Entity\ProductColor;
use App\Entity\ProductSize;
use App\Entity\Tags;
use App\Repository\ProductColorRepository;
use App\Repository\ProductSizeRepository;
use App\Repository\TagsRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\HttpFoundation\ParameterBag;
use Symfony\Component\HttpFoundation\Request;

final class SizeRequestParser
{
    use ParserTrait;

    private \Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface $parameterBag;

    private \App\Repository\ProductSizeRepository $sizeRepository;

    /**
     * SizeRequestParser constructor.
     */
    public function __construct(
        ParameterBagInterface $parameterBag,
        ProductSizeRepository $sizeRepository
    ) {
        $this->parameterBag = $parameterBag;
        $this->sizeRepository = $sizeRepository;
    }

    /**
     * @param ProductSize|null $productSize
     *
     */
    public function parse(ParameterBag $bag, ProductSize $productSize = null): ProductSize
    {
        if (!$productSize instanceof ProductSize) {
            $productSize = new ProductSize();
        }

        $productSize->setSize($bag->get('title'));

        return $productSize;
    }
}