<?php

declare(strict_types=1);

namespace App\Formatter\Admin;

use App\Entity\Banner;
use App\Entity\Career;
use App\Entity\CareerDescription;
use App\Entity\Location;
use App\Entity\OrderProduct;
use App\Entity\Promotion;
use App\Entity\ShopOrder;
use App\Repository\ImageRepository;
use App\Repository\SettingsRepository;
use Symfony\Component\Routing\RouterInterface;

final class CareerDetailResponseFormatter
{
    private \Symfony\Component\Routing\RouterInterface $router;

    /**
     * @param ImageRepository    $imageRepository
     * @param SettingsRepository $settingsRepository
     */
    public function __construct(
        RouterInterface $router
    ) {
        $this->router = $router;
    }

    
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
