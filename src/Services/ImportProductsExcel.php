<?php

declare(strict_types=1);

namespace App\Services;

use App\Entity\Product;
use App\Entity\ProductColor;
use App\Entity\ProductHasCategories;
use App\Entity\ProductHasSizes;
use App\Entity\ProductHasTags;
use App\Entity\ProductTranslation;
use App\Entity\Tags;
use App\Helper\EncodingHelper;
use App\Repository\CategoryTranslationRepository;
use App\Repository\ProductColorRepository;
use App\Repository\ProductRepository;
use App\Repository\ProductSizeRepository;
use App\Repository\ProductTranslationRepository;
use App\Repository\TagsRepository;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;

final class ImportProductsExcel
{
    /**
     * @var ProductRepository
     */
    private $productRepository;

    /**
     * @var ParameterBagInterface
     */
    private $parameterBag;

    /**
     * @var ProductTranslationRepository
     */
    private $translationRepository;

    /**
     * @var CategoryTranslationRepository
     */
    private $categoryTranslationRepository;

    /**
     * @var ProductSizeRepository
     */
    private $sizeRepository;

    /**
     * @var ProductImageService
     */
    private $imageService;

    /**
     * @var ProductColorRepository
     */
    private $colorRepository;
    /**
     * @var TagsRepository
     */
    private $tagsRepository;

    /**
     * @param ProductRepository             $productRepository
     * @param ParameterBagInterface         $parameterBag
     * @param ProductTranslationRepository  $translationRepository
     * @param CategoryTranslationRepository $categoryTranslationRepository
     * @param ProductSizeRepository         $sizeRepository
     * @param ProductImageService           $imageService
     * @param ProductColorRepository        $colorRepository
     * @param TagsRepository                $tagsRepository
     */
    public function __construct(
        ProductRepository $productRepository,
        ParameterBagInterface $parameterBag,
        ProductTranslationRepository $translationRepository,
        CategoryTranslationRepository $categoryTranslationRepository,
        ProductSizeRepository $sizeRepository,
        ProductImageService $imageService,
        ProductColorRepository $colorRepository,
        TagsRepository $tagsRepository
    ) {
        $this->productRepository = $productRepository;
        $this->parameterBag = $parameterBag;
        $this->translationRepository = $translationRepository;
        $this->categoryTranslationRepository = $categoryTranslationRepository;
        $this->sizeRepository = $sizeRepository;
        $this->imageService = $imageService;
        $this->colorRepository = $colorRepository;
        $this->tagsRepository = $tagsRepository;
    }

    public function doImport()
    {
        if ($xslx = \SimpleXLSX::parse(__DIR__.'/../../storage/import/product_import.xlsx')) {
            $rows = $xslx->rows();
            $rootDir = $this->parameterBag->get('upload_dir');
            $imagePath = $this->parameterBag->get('upload_import_dir');
            $color = $this->colorRepository->findOneBy([]);

            $keys = $rows[0];

            unset($rows[0]);

            $data = $rows;

            foreach ($data as $product) {
                $formattedProduct = array_combine($keys, $product);
                $product = $this->getProduct($formattedProduct);
                $images = [];
                $encoded = EncodingHelper::toISO8859($formattedProduct['naziv_rs']);

                foreach (glob($rootDir . $imagePath . $encoded . '*') as $index => $image) {
                    $imageInfo = pathinfo($image);

                    $images[] = [
                        'fileName' => $imageInfo['filename'].'.'.$imageInfo['extension'],
                        'isMain' => $index === 0,
                        'color' => $color->getId(),
                    ];
                }

                $product->setCode((string) $formattedProduct['šifra'])
                    ->setDiscount((int) $formattedProduct['popust'])
                    ->setPrice((int) $formattedProduct['cena'])
                    ->setShowHomePage(0);

                dump($formattedProduct['šifra']);
                $this->setLocales($formattedProduct, $product);
                $this->setCategories($product, explode(',', $formattedProduct['kategorije']));
                $this->setTags($product, explode(',', $formattedProduct['tagovi']));
                $this->setSizes($product, explode(',', (string) $formattedProduct['velicine']));
                $this->imageService->setImages($product->getProductTranslations()->first(), $images,true);


                if (null === $product->getId()) {
                    $this->productRepository->persist($product);
                }

                $this->productRepository->flush();
            }
        } else {
            \SimpleXLSX::parseError();
        }


    }

    /**
     * @param array $productExcel
     *
     * @return Product
     */
    private function getProduct(array $productExcel): Product
    {
        $product = $this->productRepository->findOneBy([
            'code' => $productExcel['šifra'],
        ]);

        if (null === $product) {
            $product = new Product();
            $product->setStatus(Product::STATUS_PENDING);

        }

        return $product;
    }

    /**
     * @param array   $productExcel
     * @param Product $product
     */
    private function setLocales(array $productExcel, Product $product): void
    {
        $locales = explode('|', $this->parameterBag->get('locales'));

        foreach ($locales as $locale) {
            $trans = new ProductTranslation();

            if (!is_null($product->getId())) {
                $trans = $this->translationRepository->findOneBy(['product' => $product, 'locale' => $locale]);
            }

            $trans->setTitle($productExcel['naziv_'.$locale]);
            $trans->setDescription($productExcel['detaljan_opis_'.$locale]);
            $trans->setShortDescription($productExcel['kratak_opis_'.$locale]);
            $trans->setLocale($locale);

            $product->addProductTranslation($trans);
        }
    }

    /**
     * @param Product $product
     * @param array   $categories
     */
    private function setCategories(Product $product, array $categories): void
    {
        if (!is_null($product->getId())) {
            $hasCategories = $product->getProductHasCategories();
            $hasCategories->clear();
        }

        $categoriesLocale = $this->categoryTranslationRepository->findBy(['title' => $categories]);

        foreach ($categoriesLocale as $categoryTranslation) {
            $hasCategory = new ProductHasCategories();
            $hasCategory->setCategory($categoryTranslation->getCategory());
            $hasCategory->setProduct($product);

            $product->addProductHasCategory($hasCategory);
        }
    }

    /**
     * @param Product $product
     * @param array   $tags
     */
    private function setTags(Product $product, array $tags): void
    {
        if (!is_null($product->getId())) {
            $hasTags = $product->getProductHasTags();
            $hasTags->clear();
        }

        foreach ($tags as $tag) {
            $tagEntity = $this->tagsRepository->findOneBy(['label' => trim($tag), 'locale' => 'rs', 'relatedType' => Tags::TYPE_PRODUCT]);

            if (null === $tagEntity) {
                continue;
            }
            $hasTags = new ProductHasTags();
            $hasTags->setTag($tagEntity->getMainSlug());

            $product->addProductHasTag($hasTags);
        }
    }

    /**
     * @param Product $product
     * @param array   $sizes
     */
    private function setSizes(Product $product, array $sizes): void
    {
        if (!is_null($product->getId())) {
            $hasTags = $product->getProductHasSizes();
            $hasTags->clear();
        }

        $sizeCollection = $this->sizeRepository->findBy(['size' => $sizes]);

        foreach ($sizeCollection as $size) {
            $hasSize = new ProductHasSizes();
            $hasSize->setSize($size);
            $hasSize->setIsAvailable(true);

            $product->addProductHasSize($hasSize);
        }
    }
}