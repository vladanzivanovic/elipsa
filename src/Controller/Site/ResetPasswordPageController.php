<?php

declare(strict_types=1);

namespace App\Controller\Site;

use App\Repository\UserRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;

final class ResetPasswordPageController extends AbstractController
{
    /**
     * @var UserRepository
     */
    private $repository;
    /**
     * @var TranslatorInterface
     */
    private $translator;

    /**
     * @param UserRepository      $repository
     * @param TranslatorInterface $translator
     */
    public function __construct(
        UserRepository $repository,
        TranslatorInterface $translator
    ) {
        $this->repository = $repository;
        $this->translator = $translator;
    }

    /**
     * @Route({
     *          "rs": "/promena-lozinke/{resetToken}",
     *          "en": "/reset-password/{resetToken}"
     *     },
     *     name="site.reset_password_page",
     *     methods={"GET"}
     * )
     * @Template("Site/Pages/resetPasswordPage.html.twig")
     *
     * @param string $resetToken
     *
     * @return array|RedirectResponse
     */
    public function index(string $resetToken)
    {
        $user = $this->repository->findOneBy(['resetToken' => $resetToken]);

        if (null === $user) {
            return new RedirectResponse($this->generateUrl('site.not_exists_page'), 302);
        }

        $tokenDate = $user->getResetRequestAt();
        $diff = $tokenDate->diff(new \DateTime())->format('%a');

        if ($diff > 7) {
            $this->addFlash('message', $this->translator->trans('reset_password.page.error.expired_token'));
        }

        return [];
    }
}