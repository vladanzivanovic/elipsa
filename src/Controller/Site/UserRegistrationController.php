<?php

declare(strict_types=1);

namespace App\Controller\Site;

use App\Entity\User;
use App\Handler\Site\UserHandler;
use Symfony\Bridge\Twig\Attribute\Template;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;

final class UserRegistrationController extends AbstractController
{
    private \App\Handler\Site\UserHandler $handler;

    public function __construct(
        UserHandler $handler
    ) {
        $this->handler = $handler;
    }

    /**
     *
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    #[Route(path: [
        'rs' => '/aktivacija-naloga/{resetToken}',
        'en' => '/account-activation/{resetToken}',
        'ba' => '/aktivacija-naloga/{resetToken}'
    ], name: 'site.registration_activation_page', methods: ['GET'])]
    #[Template('Site/Pages/accountActivationPage.html.twig')]
    public function index(User $user): array
    {
        if($user->getStatus() == User::STATUS_ACTIVE) {
            return ['error' => 1];
        }
        if($user->getStatus() == User::STATUS_DISABLED) {
            return ['error' => 2];
        }

        $user->setStatus(User::STATUS_ACTIVE);
        $user->setResetRequestAt(null);
        $user->setResetToken(null);

        $this->handler->save($user);

        return [];
    }
}
