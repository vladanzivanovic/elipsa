<?php

declare(strict_types=1);

namespace App\Checker;

use App\Entity\User;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Security\Core\Exception\CustomUserMessageAccountStatusException;
use Symfony\Component\Security\Core\User\UserCheckerInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

class AuthenticationChecker implements UserCheckerInterface
{
    private TranslatorInterface $translator;

    private RequestStack $requestStack;

    public function __construct(
        TranslatorInterface $translator,
        RequestStack $requestStack
    ){
        $this->translator = $translator;
        $this->requestStack = $requestStack;
    }

    public function checkPreAuth(UserInterface $user)
    {
        if (!$user instanceof User) {
            return;
        }

        if(User::STATUS_PENDING === $user->getStatus()){
            throw new CustomUserMessageAccountStatusException(
                $this->translator->trans(
                    'user.inactive_user',
                    [],
                    null,
                    $this->requestStack->getCurrentRequest()->headers->get('Content-Language')
                )
            );
        }
    }

    public function checkPostAuth(UserInterface $user)
    {
        // TODO: Implement checkPostAuth() method.
    }
}
