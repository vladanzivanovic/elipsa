<?php

declare(strict_types=1);

namespace App\Controller\Site\Api;

use App\Handler\Site\ResetPasswordHandler;
use App\Handler\Site\UserHandler;
use App\Parser\Site\ResetPasswordRequestParser;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;

final class ResetPasswordController extends AbstractController
{
    /**
     * @var ResetPasswordRequestParser
     */
    private $requestParser;

    /**
     * @var ResetPasswordHandler
     */
    private $handler;

    /**
     * @var TranslatorInterface
     */
    private $translator;

    /**
     * @param ResetPasswordRequestParser $requestParser
     * @param ResetPasswordHandler       $handler
     * @param TranslatorInterface        $translator
     */
    public function __construct(
        ResetPasswordRequestParser $requestParser,
        ResetPasswordHandler $handler,
        TranslatorInterface $translator
    ) {
        $this->requestParser = $requestParser;
        $this->handler = $handler;
        $this->translator = $translator;
    }

    /**
     * @Route({
     *          "rs": "/api/ask-for-reset-password",
     *          "en": "/api/ask-for-reset-password"
     *      },
     *     name="site_api.user_ask_for_reset_password",
     *     methods={"PATCH"},
     *     options={"expose": true}
     * )
     * @param Request $request
     *
     * @return JsonResponse
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function requestForResetPassword(Request $request): JsonResponse
    {
        $csrf = $request->request->get('_csrf_token');

        if (false === $this->isCsrfTokenValid('reset_password', $csrf)) {
            $this->createAccessDeniedException();
        }

        try {
            $user = $this->requestParser->parse($request->request->get('reset_email'));

            $this->handler->askForResetPassword($user, $request->getLocale());
            $request->getSession()->getFlashBag()->add('message', $this->translator->trans('reset_password.successful'));

        } catch (BadRequestHttpException $httpException) {
            return $this->json(['error' => $httpException->getMessage()], JsonResponse::HTTP_BAD_REQUEST);
        }

        return $this->json(null, JsonResponse::HTTP_CREATED);
    }

    /**
     * @Route({
     *          "rs": "/api/reset-password",
     *          "en": "/api/reset-password"
     *      },
     *     name="site_api.user_reset_password",
     *     methods={"PUT"},
     *     options={"expose": true}
     * )
     * @param Request $request
     *
     * @return JsonResponse
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function resetPassword(Request $request): JsonResponse
    {
        $csrf = $request->request->get('_csrf_token');

        if (false === $this->isCsrfTokenValid('reset_password_form', $csrf)) {
            $this->createAccessDeniedException();
        }

        try {
            $user = $this->requestParser->parseResetPassword($request->request);

            $this->handler->resetPassword($user);
            $request->getSession()->getFlashBag()->add('message', $this->translator->trans('reset_password.page.success'));

        } catch (BadRequestHttpException $httpException) {
            return $this->json(['error' => $httpException->getMessage()], JsonResponse::HTTP_BAD_REQUEST);
        }

        return $this->json(null, JsonResponse::HTTP_CREATED);
    }
}