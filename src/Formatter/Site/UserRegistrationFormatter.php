<?php

declare(strict_types=1);

namespace App\Formatter\Site;

use App\Entity\User;
use App\View\UserView;

final class UserRegistrationFormatter
{
    private UserView $userView;

    public function __construct(
        UserView $userView
    ) {
        $this->userView = $userView;
    }
    public function formatResponse(User $user, string $locale): array
    {
        $view = $this->userView->view($user);

        unset($view['email']);

        $view['locale'] = $locale;

        return $view;
    }
}
