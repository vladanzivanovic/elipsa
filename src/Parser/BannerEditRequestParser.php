<?php

declare(strict_types=1);

namespace App\Parser;

use App\Entity\Banner;
use App\Entity\BannerTranslation;
use App\Entity\Image;
use App\Repository\BannerRepository;
use App\Services\BannerImageService;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\HttpFoundation\ParameterBag;

final class BannerEditRequestParser
{
    use ParserTrait;

    /**
     * @var ParameterBagInterface
     */
    private $parameterBag;

    /**
     * @var BannerImageService
     */
    private $imageService;

    /**
     * @var BannerRepository
     */
    private $bannerRepository;

    /**
     * BannerEditRequestParser constructor.
     *
     * @param ParameterBagInterface $parameterBag
     * @param BannerImageService    $imageService
     * @param BannerRepository      $bannerRepository
     */
    public function __construct(
        ParameterBagInterface $parameterBag,
        BannerImageService $imageService,
        BannerRepository $bannerRepository
    ) {
        $this->parameterBag = $parameterBag;
        $this->imageService = $imageService;
        $this->bannerRepository = $bannerRepository;
    }

    /**
     * @param ParameterBag $bag
     * @param Banner|null  $banner
     *
     * @return Banner
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

    private function setLocale(ParameterBag $bag, Banner $banner)
    {
        $locales = $this->setLanguageArray($this->parameterBag, $bag);

        foreach ($locales as $locale => $lagBag) {
            $trans = new BannerTranslation();

            if (null !== $banner->getId()) {
                $trans = $banner->getByLocale($locale);
            }

            $trans->setDescription($lagBag->get('description', ''));
            $trans->setButtonText($lagBag->get('button'));
            $trans->setButtonLink($lagBag->get('link'));
            $trans->setLocale($locale);

            $banner->addBannerTranslation($trans);
        }
    }
}