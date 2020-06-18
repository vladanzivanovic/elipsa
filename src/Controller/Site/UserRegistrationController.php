<?php

declare(strict_types=1);

namespace App\Controller\Site;

use App\Collector\CartPageCollector;
use App\Entity\User;
use App\Formatter\Site\CartPageFormatter;
use App\Handler\Site\UserHandler;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

final class UserRegistrationController extends AbstractController
{
    /**
     * @var CartPageCollector
     */
    private $pageCollector;
    /**
     * @var CartPageFormatter
     */
    private $pageFormatter;
    /**
     * @var UserHandler
     */
    private $handler;

    /**
     * CartPageController constructor.
     *
     * @param CartPageCollector $pageCollector
     * @param CartPageFormatter $pageFormatter
     * @param UserHandler       $handler
     */
    public function __construct(
        CartPageCollector $pageCollector,
        CartPageFormatter $pageFormatter,
        UserHandler $handler
    ) {
        $this->pageCollector = $pageCollector;
        $this->pageFormatter = $pageFormatter;
        $this->handler = $handler;
    }

    /**
     * @Route({
     *          "rs": "/aktivacija-naloga/{resetToken}",
     *          "en": "/account-activation/{resetToken}"
     *     },
     *     name="site.registration_activation_page",
     *     methods={"GET"}
     * )
     * @Template("Site/Pages/accountActivationPage.html.twig")
     *
     * @param Request $request
     * @param User    $user
     *
     * @return array
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function index(Request $request, User $user): array
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

        $this->handler->save($user, $request->getLocale());

        return [];
    }
}