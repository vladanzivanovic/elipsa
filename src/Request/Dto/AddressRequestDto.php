<?php

declare(strict_types=1);

namespace App\Request\Dto;

final class AddressRequestDto
{
    public string $street;

    public string $city;

    public int $zipCode;

    public string $country;

    public string $mobilePhone;

    public function __construct(array $address)
    {
        $this->street = $address['street'];
        $this->city = $address['city'];
        $this->zipCode = (int) $address['zip_code'];
        $this->country = $address['country'];
        $this->mobilePhone = $address['mobile_phone'];
    }
}
