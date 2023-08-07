<?php

declare(strict_types=1);

namespace App\View;

use App\Entity\Address;

final class AddressView
{
    public function view(Address $address): array
    {
        return [
            'first_name' => $address->getFirstName(),
            'last_name' => $address->getLastName(),
            'email' => $address->getEmail(),
            'country' => $address->getCountry(),
            'post_code' => $address->getZipCode(),
            'street' => $address->getAddress(),
            'city' => $address->getCity(),
            'telephone' => $address->getPhone(),
        ];
    }
}
