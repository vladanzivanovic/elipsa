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
     * @param Banner $banner
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
}