<?php

declare(strict_types=1);

namespace App\Formatter\Site\Router;

use App\Repository\CategoryTranslationRepository;
use App\Repository\ColorTranslationRepository;
use App\Repository\TagsRepository;
use App\ShopTrait;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\HttpFoundation\ParameterBag;
use Symfony\Contracts\Translation\TranslatorInterface;

final class BlogListRouterFormatter
{
    /**
     * @var ParameterBagInterface
     */
    private $bag;

    /**
     * @var TranslatorInterface
     */
    private $translator;

    /**
     * @var TagsRepository
     */
    private $tagsRepository;
    /**
     * @var ColorTranslationRepository
     */
    private $colorTranslationRepository;
    /**
     * @var CategoryTranslationRepository
     */
    private $categoryTranslationRepository;

    /**
     * @param ParameterBagInterface         $bag
     * @param TranslatorInterface           $translator
     * @param TagsRepository                $tagsRepository
     * @param ColorTranslationRepository    $colorTranslationRepository
     * @param CategoryTranslationRepository $categoryTranslationRepository
     */
    public function __construct(
        ParameterBagInterface $bag,
        TranslatorInterface $translator,
        TagsRepository $tagsRepository,
        ColorTranslationRepository $colorTranslationRepository,
        CategoryTranslationRepository $categoryTranslationRepository
    ) {
        $this->tagsRepository = $tagsRepository;
        $this->bag = $bag;
        $this->translator = $translator;
        $this->colorTranslationRepository = $colorTranslationRepository;
        $this->categoryTranslationRepository = $categoryTranslationRepository;
    }

    /**
     * @param string $tag
     * @param string $locale
     *
     * @return int|mixed|string
     * @throws \Doctrine\ORM\NoResultException
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function localeFormatter(string $tag, string $locale)
    {
        return $this->tagsRepository->getForLocalization($tag, $locale);
    }
}