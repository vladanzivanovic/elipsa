<?php

declare(strict_types=1);

namespace App\Parser\Site;

use App\Entity\Address;
use Symfony\Component\HttpFoundation\ParameterBag;

final class AddressParser
{
    public function parse(ParameterBag $bag)
    {
        $address = $this->create();

        $address->setFirstName($bag->get('first_name'));
        $address->setLastName($bag->get('last_name'));
        $address->setEmail($bag->get('email'));
        $address->setCountry($bag->get('country'));
        $address->setCity($bag->get('city'));
        $address->setAddress($bag->get('street'));
        $address->setPhone($bag->get('mobile_phone'));
        $address->setZipCode($bag->getInt('zip_code'));

        return $address;
    }

    private function create(): Address
    {
        return new Address();
    }
}
