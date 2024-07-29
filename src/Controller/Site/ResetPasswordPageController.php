<?php

declare(strict_types=1);

namespace App\Controller\Site;

use App\Repository\UserRepository;
use Symfony\Bridge\Twig\Attribute\Template;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Contracts\Translation\TranslatorInterface;

final class ResetPasswordPageController extends AbstractController
{
    private \App\Repository\UserRepository $repository;
    private \Symfony\Contracts\Translation\TranslatorInterface $translator;

    public function __construct(
        UserRepository $repository,
        TranslatorInterface $translator
    ) {
        $this->repository = $repository;
        $this->translator = $translator;
    }

    /**
     *
     * @return array|RedirectResponse
     */
    #[Route(path: ['rs' => '/promena-lozinke/{resetToken}', 'en' => '/reset-password/{resetToken}'], name: 'site.reset_password_page', methods: ['GET'])]
    #[Template('Site/Pages/resetPasswordPage.html.twig')]
    public function index(string $resetToken)
    {
        $user = $this->repository->findOneBy(['resetToken' => $resetToken]);

        if (null === $user) {
            return new RedirectResponse($this->generateUrl('site.not_exists_page'), \Symfony\Component\HttpFoundation\Response::HTTP_FOUND);
        }

        $tokenDate = $user->getResetRequestAt();
        $diff = $tokenDate->diff(new \DateTime())->format('%a');

        if ($diff > 7) {
            $this->addFlash('message', $this->translator->trans('reset_password.page.error.expired_token'));
        }

        return [];
    }
}
