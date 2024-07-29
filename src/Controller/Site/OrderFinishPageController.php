<?php

declare(strict_types=1);

namespace App\Controller\Site;

use App\Exception\OrderException;
use App\Formatter\Site\OrderFinishPageFormatter;
use App\Handler\Site\OrderHandler;
use App\Mailer\OrderCompleteMailer;
use App\Parser\Site\Order\OrderFinishParser;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use Symfony\Bridge\Twig\Attribute\Template;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Contracts\Translation\TranslatorInterface;

final class OrderFinishPageController extends AbstractController
{
    public function __construct(
        private readonly OrderHandler $handler,
        private readonly OrderFinishPageFormatter $pageFormatter,
        private readonly OrderCompleteMailer $orderCompleteMailer,
        private readonly OrderFinishParser $orderFinishParser,
        private readonly TranslatorInterface $translator,
    ) {}

    /**
     *
     * @return array|RedirectResponse
     *
     * @throws NonUniqueResultException
     * @throws ORMException
     * @throws OptimisticLockException
     * @throws \ReflectionException
     */
    #[Route(path: [
        'rs' => '/korpa/uspesna-narudzbina/{token}',
        'en' => '/cart/success-order/{token}',
        'ba' => '/korpa/uspjesna-narudzba/{token}'
    ], name: 'site.checkout_completed_successful', options: ['expose' => true], methods: ['POST', 'GET'])]
    #[Template('Site/Pages/checkoutFinish.html.twig')]
    public function successPage(Request $request, string $token)
    {
        $locale = $request->attributes->get('_locale');

        $isSuccessfulTransaction = true;

        try {
            $order = $this->orderFinishParser->parse($token, $isSuccessfulTransaction, $request->getMethod() === Request::METHOD_POST ? $request->request : null);
        } catch (OrderException $orderException) {
            $request->getSession()->getFlashBag()->add(
                'message',
                $this->translator->trans(
                    $orderException->getMessage(),
                    $orderException->getParameters(),
                    $orderException->getDomain(),
                    $locale
                )
            );

            return $this->redirectToRoute('site.home_page');
        }

        $this->handler->save($order, 'CompleteOrderOnSuccess');

        $viewData = $this->pageFormatter->formatResponse(
            $order,
            $locale,
            $isSuccessfulTransaction
        );

        $this->orderCompleteMailer->sendEmail($viewData, $order, $locale);

        return $viewData;
    }

    #[Route(path: [
        'rs' => '/korpa/neuspesna-narudzbina/{token}',
        'en' => '/cart/unsuccessful-order/{token}',
        'ba' => '/korpa/neuspjesna-narudzba/{token}'
    ], name: 'site.checkout_failed', options: ['expose' => true], methods: ['POST', 'GET'])]
    #[Template('Site/Pages/checkoutFinish.html.twig')]
    public function unsuccessfulPage(Request $request, string $token)
    {
        $locale = $request->attributes->get('_locale');

        $isSuccessfulTransaction = false;

        try {
            $order = $this->orderFinishParser->parse($token, $isSuccessfulTransaction, $request->getMethod() === Request::METHOD_POST ? $request->request : null);
        } catch (OrderException $orderException) {
            $request->getSession()->getFlashBag()->add(
                'message',
                $this->translator->trans(
                    $orderException->getMessage(),
                    $orderException->getParameters(),
                    $orderException->getDomain(),
                    $locale
                )
            );

            return $this->redirectToRoute('site.home_page');
        }

        $this->handler->save($order, 'FailedOrder');

        $viewData = $this->pageFormatter->formatResponse(
            $order,
            $locale,
            $isSuccessfulTransaction
        );

        $this->orderCompleteMailer->sendEmail($viewData, $order, $locale);

        return $viewData;
    }
}
