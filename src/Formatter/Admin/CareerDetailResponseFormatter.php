<?php

declare(strict_types=1);

namespace App\Formatter\Admin;

use App\Entity\Banner;
use App\Entity\Career;
use App\Entity\CareerDescription;
use App\Entity\Location;
use App\Entity\OrderProduct;
use App\Entity\PromotionCoupon;
use App\Entity\ShopOrder;
use App\Repository\ImageRepository;
use App\Repository\SettingsRepository;
use Symfony\Component\Routing\RouterInterface;

final class CareerDetailResponseFormatter
{
    /**
     * @var RouterInterface
     */
    private $router;

    /**
     * @var ImageRepository
     */
    private $imageRepository;
    /**
     * @var SettingsRepository
     */
    private $settingsRepository;

    /**
     * @param RouterInterface    $router
     * @param ImageRepository    $imageRepository
     * @param SettingsRepository $settingsRepository
     */
    public function __construct(
        RouterInterface $router,
        ImageRepository $imageRepository,
        SettingsRepository $settingsRepository
    ) {
        $this->router = $router;
        $this->imageRepository = $imageRepository;
        $this->settingsRepository = $settingsRepository;
    }

    /**
     * @param Career $career
     *
     * @return array
     */
    public function formatResponse(Career $career): array
    {
        return [
            'id' => $career->getId(),
            'fullName' => $career->getFirstName().' '.$career->getLastName(),
            'email' => $career->getEmail(),
            'mobilePhone' => $career->getMobilePhone(),
            'address' => $career->getAddress(),
            'city' => $career->getCity(),
            'applyingDate' => $career->getCreatedAt()->format('d.m.Y'),
            'school' => $career->getSchool(),
            'schoolLevel' => $career->getSchoolLevel(),
            'schoolTitle' => $career->getSchoolTitle(),
            'position' => $career->getPosition()->getTranslationByLocale('rs')->getTitle(),
            'cv' => $this->router->generate('app.download_doc', ['id' => $career->getCv()->getId()], RouterInterface::ABSOLUTE_URL),
            'accompanyingLetter' => $career->getAccompanyingLetter(),
            'birthDate' => $career->getBirthDate()->format('d.m.Y'),
        ];
    }
}