<?php

declare(strict_types=1);

namespace App\Parser;

use App\Entity\Banner;
use App\Entity\BannerTranslation;
use App\Entity\Image;
use App\Repository\BannerTranslationRepository;
use App\Services\BannerImageService;
use Symfony\Component\HttpFoundation\ParameterBag;

final class BannerEditRequestParser
{
    private array $locales;

    public function __construct(
        private readonly BannerImageService $imageService,
        private readonly BannerTranslationRepository $translationRepository,
        string $locales,
    ) {
        $this->locales = explode('|', $locales);
    }

    /**
     * @param Banner|null  $banner
     * @throws \Doctrine\ORM\ORMException
     */
    public function parse(ParameterBag $bag, Banner $banner = null): Banner
    {
        if (!$banner instanceof Banner) {
            $banner = new Banner();
            $banner->setIsActive(false);
        }

        $banner->setPosition($bag->getInt('position'));
        $banner->setType($bag->getInt('type'));

        $this->setLocale($bag, $banner);

        $this->imageService->setImages($banner, json_decode($bag->get('images'), true), Image::DEVICE_DESKTOP);
        if ($bag->has('images_mobile')) {
            $this->imageService->setImages($banner, json_decode($bag->get('images_mobile'), true), Image::DEVICE_MOBILE);
        }

        return $banner;
    }

    private function setLocale(ParameterBag $bag, Banner $banner): void
    {
        foreach ($this->locales as $locale) {
            $transCollection = $bag->all($locale);
            $trans = $this->translationRepository->findOneBy(['banner' => $banner, 'locale' => $locale]);


            if (null === $trans) {
                $trans = new BannerTranslation();
            }

            $trans->setDescription($transCollection['description'] ?? '');
            $trans->setButtonText($transCollection['button']);
            $trans->setButtonLink($transCollection['link']);
            $trans->setLocale($locale);

            $banner->addBannerTranslation($trans);
        }
    }
}
