<?php

declare(strict_types=1);

namespace App\View;

use App\Entity\User;

final class UserView
{
    public function view(User $user)
    {
        $view = [
            'id' => $user->getId(),
            'first_name' => $user->getFirstName(),
            'last_name' => $user->getLastName(),
            'email' => $user->getEmail(),
            'reset_token' => $user->getResetToken(),
        ];

        return $view;
    }
}
