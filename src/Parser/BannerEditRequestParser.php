<?php

declare(strict_types=1);

namespace App\Parser;

use App\Entity\Banner;
use App\Entity\BannerTranslation;
use App\Entity\Image;
use App\Repository\BannerTranslationRepository;
use App\Request\Dto\Admin\BannerEditRequestDto;
use App\Services\BannerImageService;
use Symfony\Component\HttpFoundation\ParameterBag;

final class BannerEditRequestParser
{
    public function __construct(
        private readonly BannerImageService $imageService,
        private readonly BannerTranslationRepository $translationRepository,
        private readonly array $locales,
    ) {}

    /**
     * @param Banner|null  $banner
     * @throws \Doctrine\ORM\ORMException
     */
    public function parse(BannerEditRequestDto $bannerEditRequestDto, Banner $banner = null): Banner
    {
        if (!$banner instanceof Banner) {
            $banner = new Banner();
            $banner->setIsActive(false);
        }

        $banner->setPosition($bannerEditRequestDto->position);
        $banner->setType($bannerEditRequestDto->type);
        $banner->setAvailableCountries($bannerEditRequestDto->available_countries);

        $this->setLocale($bannerEditRequestDto->translations, $banner);

        foreach ($bannerEditRequestDto->images as $device => $images) {
            $this->imageService->setImages($banner, $images, $device);
        }

        return $banner;
    }

    private function setLocale(array $translations, Banner $banner): void
    {
        foreach ($this->locales as $locale) {
            $trans = $this->translationRepository->findOneBy(['banner' => $banner, 'locale' => $locale]);

            if (null === $trans) {
                $trans = new BannerTranslation();
            }

            $trans->setDescription($translations[$locale]['description'] ?? '');
            $trans->setButtonText($translations[$locale]['button']);
            $trans->setButtonLink($translations[$locale]['link']);
            $trans->setLocale($locale);

            $banner->addBannerTranslation($trans);
        }
    }
}
