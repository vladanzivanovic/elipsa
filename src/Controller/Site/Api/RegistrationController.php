<?php

declare(strict_types=1);

namespace App\Controller\Site\Api;

use App\Formatter\Site\UserRegistrationFormatter;
use App\Handler\Site\UserHandler;
use App\Mailer\UserRegistrationMailer;
use App\Parser\Site\RegistrationRequestParser;
use App\Request\Dto\RegistrationRequestDto;
use App\View\ExceptionView;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;

final class RegistrationController extends AbstractController
{
    private RegistrationRequestParser $registrationRequestParser;

    private TranslatorInterface $translator;

    private UserHandler $userHandler;

    private ExceptionView $exceptionView;

    private UserRegistrationMailer $userRegistrationMailer;

    private UserRegistrationFormatter $userRegistrationFormatter;

    public function __construct(
        RegistrationRequestParser $registrationRequestParser,
        TranslatorInterface $translator,
        UserHandler $userHandler,
        ExceptionView $exceptionView,
        UserRegistrationMailer $userRegistrationMailer,
        UserRegistrationFormatter $userRegistrationFormatter
    ){
        $this->registrationRequestParser = $registrationRequestParser;
        $this->translator = $translator;
        $this->userHandler = $userHandler;
        $this->exceptionView = $exceptionView;
        $this->userRegistrationMailer = $userRegistrationMailer;
        $this->userRegistrationFormatter = $userRegistrationFormatter;
    }

    /**
     * @Route("/api/user/register",
     *     name="site_api.user.register",
     *     methods={"POST"},
     *     options={"expose": true})
     */
    public function register(RegistrationRequestDto $registrationRequestDto): JsonResponse
    {
        if (false === $this->isCsrfTokenValid('user_registration', $registrationRequestDto->csrf)) {
            $this->createAccessDeniedException();
        }

        try {
            $user = $this->registrationRequestParser->parse($registrationRequestDto);

            $this->userHandler->save($user, 'SetUser');

            $viewData = $this->userRegistrationFormatter->formatResponse($user, $registrationRequestDto->locale);

            $this->userRegistrationMailer->sendEmail($viewData, $user);

        } catch (\Throwable $throwable) {
            return $this->json(
                ['error' => $this->exceptionView->view($throwable, $registrationRequestDto->locale)],
                Response::HTTP_BAD_REQUEST
            );
        }

        return $this->json(null, Response::HTTP_CREATED);
    }
}
