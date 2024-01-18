<?php

declare(strict_types=1);

namespace App\View;

use App\Entity\User;

final class UserView
{
    private AddressView $addressView;

    public function __construct(
        AddressView $addressView
    ){
        $this->addressView = $addressView;
    }

    public function view(User $user)
    {
        $view = [
            'id' => $user->getId(),
            'first_name' => $user->getFirstName(),
            'last_name' => $user->getLastName(),
            'email' => $user->getEmail(),
            'reset_token' => $user->getResetToken(),
            'address' => null,
        ];

        $address = $user->getAddress();

        if (null !== $address) {
            $view['address'] = $this->addressView->view($address);
        }

        return $view;
    }
}
