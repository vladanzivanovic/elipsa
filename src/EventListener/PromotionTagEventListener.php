<?php

declare(strict_types=1);

namespace App\EventListener;

use App\Entity\Product;
use App\Entity\ProductHasTags;
use App\Entity\Promotion;
use App\Entity\PromotionOption;
use App\Entity\Tags;
use App\Entity\TagTranslation;
use App\Event\PromotionTagEvent;
use App\Repository\ProductRepository;
use App\Repository\TagsRepository;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

#[AsEventListener]
final class PromotionTagEventListener
{
    public function __construct(
        private readonly ProductRepository $productRepository,
        private readonly TagsRepository $tagsRepository,
        private readonly TranslatorInterface $translator,
        private readonly RouterInterface $router,
        private readonly array $locales,
    ) {}

    public function __invoke(PromotionTagEvent $event): void
    {
        $promotion = $event->getPromotion();

        $this->removeRelations($promotion);

        $homeBarOption = $promotion->getOptionByType(PromotionOption::OPTION_HOME_SCREEN_BAR);

        if (false === $homeBarOption->getConfiguration()[0]) {
            return;
        }

        $tag = $this->createTag($promotion);

        $options = $promotion->getOptionRules();

        $isApplicableToDiscountedProducts = filter_var(
            $promotion->getOptionByType(PromotionOption::RULE_ALL_PRODUCTS)->getConfiguration()[0],
            FILTER_VALIDATE_BOOLEAN
        );

        $products = [];

        foreach ($options as $option) {
            $matchedProducts = match ($option->getType()) {
                PromotionOption::RULE_PRODUCTS => $this->productRepository->getProductsByIDsFromPromotion(
                    $option->getConfiguration(),
                    $promotion->getAvailableCountries(),
                    $isApplicableToDiscountedProducts
                ),
                PromotionOption::RULE_CATEGORIES => $this->productRepository->getProductsByCategoriesFromPromotion(
                    $option->getConfiguration(),
                    $promotion->getAvailableCountries(),
                    $isApplicableToDiscountedProducts
                ),
                PromotionOption::RULE_TAGS => $this->productRepository->getProductsByTagsFromPromotion(
                    $option->getConfiguration(),
                    $promotion->getAvailableCountries(),
                    $isApplicableToDiscountedProducts
                ),
                PromotionOption::RULE_COLORS => $this->productRepository->getProductsByColorsFromPromotion(
                    $option->getConfiguration(),
                    $promotion->getAvailableCountries(),
                    $isApplicableToDiscountedProducts
                ),
            };

            $products = \array_merge_recursive(
                $products,
                $matchedProducts,
            );
        }

        /** @var Product $product */
        foreach ($products as $product) {
            if (true === $product->isProductHasTag($tag)) {
                continue;
            }

            $shouldSetTag = false;

            foreach ($promotion->getAvailableCountries() as $availableCountry) {
                if (true === $shouldSetTag) {
                    continue;
                }

                $productOption = $product->getOptionsByCountry($availableCountry);

                if (
                    false === $isApplicableToDiscountedProducts &&
                    ($productOption?->getDiscount() > 0)
                ) {
                    continue;
                }

                $shouldSetTag = true;
            }

            if (true === $shouldSetTag) {
                $productHasTag = new ProductHasTags();

                $productHasTag->setProduct($product);
                $productHasTag->setTag($tag);

                $product->addProductHasTag($productHasTag);

                $this->productRepository->persist($product);
            }
        }

        $promotion->setTags($tag);

        $homeBarOption->addConfiguration([
            'tag_translations' => $promotion->getTagTranslations(),
        ]);
    }

    private function removeRelations(Promotion $promotion): void
    {
        $tag = $promotion->getTags();

        if (null === $tag) {
            return;
        }

        $promotion->setTags(null);

        $this->tagsRepository->removeWithFlush($tag);
    }

    private function createTag(Promotion $promotion)
    {
        $tag = new Tags();
        $tag->setProductType(Tags::PRODUCT_TYPE_PROMOTION);
        $tag->setRelatedType(Tags::TYPE_PRODUCT);

        $this->setTagLocales($tag, $promotion);

        return $tag;
    }

    private function setTagLocales(Tags $tags, Promotion $promotion): void
    {
        foreach ($this->locales as $locale) {

            $trans = new TagTranslation();

            $trans->setTitle($promotion->getTagTranslations()[$locale]['title']);
            $trans->setLocale($locale);

            $tags->addTagTranslation($trans);
        }
    }
}
