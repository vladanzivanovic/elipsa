<?php

declare(strict_types=1);

namespace App\Controller\Site\Api;

use App\Handler\Site\UserHandler;
use App\Mailer\ResetPasswordRequestMailer;
use App\Parser\Site\ResetPasswordRequestParser;
use App\Request\Dto\ResetPasswordRequestDto;
use App\Request\Dto\ResetPasswordSetRequestDto;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;

final class ResetPasswordController extends AbstractController
{
    private ResetPasswordRequestParser $requestParser;

    private TranslatorInterface $translator;

    private UserHandler $userHandler;

    private ResetPasswordRequestMailer $resetPasswordRequestMailer;

    public function __construct(
        ResetPasswordRequestParser $requestParser,
        TranslatorInterface $translator,
        UserHandler $userHandler,
        ResetPasswordRequestMailer $resetPasswordRequestMailer
    ) {
        $this->requestParser = $requestParser;
        $this->translator = $translator;
        $this->userHandler = $userHandler;
        $this->resetPasswordRequestMailer = $resetPasswordRequestMailer;
    }

    /**
     * @Route("/api/ask-for-reset-password",
     *     name="site_api.user_ask_for_reset_password",
     *     methods={"PATCH"},
     *     options={"expose": true}
     * )
     *
     * @param ResetPasswordRequestDto $resetPasswordRequestDto
     *
     * @return JsonResponse
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function requestForResetPassword(ResetPasswordRequestDto $resetPasswordRequestDto): JsonResponse
    {

        if (false === $this->isCsrfTokenValid('reset_password', $resetPasswordRequestDto->csrf)) {
            $this->createAccessDeniedException();
        }

        try {
            $user = $this->requestParser->parse($resetPasswordRequestDto);

            $this->userHandler->save($user, $resetPasswordRequestDto->locale);

            $this->resetPasswordRequestMailer->sendEmail($user, $resetPasswordRequestDto->locale);

            $resetPasswordRequestDto->request->getSession()->getFlashBag()->add('message', $this->translator->trans('reset_password.successful'));

        } catch (BadRequestHttpException $httpException) {
            return $this->json(['error' => $httpException->getMessage()], Response::HTTP_BAD_REQUEST);
        }

        return $this->json(null, Response::HTTP_CREATED);
    }

    /**
     * @Route("/api/reset-password",
     *     name="site_api.user_reset_password",
     *     methods={"PUT"},
     *     options={"expose": true}
     * )
     * @param ResetPasswordSetRequestDto $resetPasswordSetRequestDto
     * @return JsonResponse
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function resetPassword(ResetPasswordSetRequestDto $resetPasswordSetRequestDto): JsonResponse
    {
        if (false === $this->isCsrfTokenValid('reset_password_form', $resetPasswordSetRequestDto->csrf)) {
            $this->createAccessDeniedException();
        }

        try {
            $user = $this->requestParser->parseResetPassword($resetPasswordSetRequestDto);

            $this->userHandler->save($user);
            $resetPasswordSetRequestDto->request->getSession()->getFlashBag()->add('message', $this->translator->trans('reset_password.page.success'));

        } catch (BadRequestHttpException $httpException) {
            return $this->json(['message' => $httpException->getMessage()], Response::HTTP_BAD_REQUEST);
        }

        return $this->json(null, Response::HTTP_CREATED);
    }
}
