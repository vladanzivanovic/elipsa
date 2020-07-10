<?php

declare(strict_types=1);

namespace App\Formatter\Admin;

use App\Entity\Address;
use App\Entity\Banner;
use App\Entity\Image;
use App\Entity\User;
use App\Repository\ImageRepository;
use Symfony\Component\Routing\RouterInterface;

final class UserEditResponseFormatter
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
     * @param RouterInterface $router
     * @param ImageRepository $imageRepository
     */
    public function __construct(
        RouterInterface $router,
        ImageRepository $imageRepository
    ) {
        $this->router = $router;
        $this->imageRepository = $imageRepository;
    }

    /**
     * @param Banner $banner
     *
     * @return array
     */
    public function formatResponse(User $user): array
    {
        /** @var Address $address */
        $address = $user->getAddress();

        return [
            'first_name' => $user->getFirstName(),
            'last_name' => $user->getLastName(),
            'email' => $user->getEmail(),
            'role' => $user->getRoles()[0],
            'address' => null !== $address ? $address->getAddress() : '',
            'city' => null !== $address ? $address->getCity() : '',
            'country' => null !== $address ? $address->getCountry() : '',
            'zip_code' => null !== $address ? $address->getZipCode() : '',
            'phone' => null !== $address ? $address->getPhone() : '',
        ];
    }

    /**
     * @param Banner $banner
     *
     * @return array
     */
    private function getImages(Banner $banner): array
    {
        $image = $banner->getImage();
        $mobileImage = $this->imageRepository->findOneBy(['parentImage' => $image->getName(), 'device' => Image::DEVICE_MOBILE]);

        $images = [
            'desktop' => [
                'id' => $image->getId(),
                'fileName' => $image->getName(),
                'isMain' => $image->getIsMain(),
            ],
        ];

        if (null !== $mobileImage) {
            $images['mobile'] = [
                'id' => $mobileImage->getId(),
                'fileName' => $mobileImage->getName(),
                'isMain' => $mobileImage->getIsMain(),
            ];
        }

        return $images;
    }
}