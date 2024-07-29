<?php

declare(strict_types=1);

namespace App\Controller\Site\Api;

use App\Handler\Site\AskUsHandler;
use App\Mailer\AskUsMailer;
use App\Parser\Site\AskUsRequestParser;
use App\Request\Dto\AskUsRequestDto;
use App\View\ExceptionView;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Contracts\Translation\TranslatorInterface;

final class AskUsController extends AbstractController
{
    private AskUsRequestParser $requestParser;

    private AskUsHandler $handler;

    private ExceptionView $exceptionView;

    private AskUsMailer $askUsMailer;

    private TranslatorInterface $translator;

    public function __construct(
        AskUsRequestParser $requestParser,
        AskUsHandler $handler,
        ExceptionView $exceptionView,
        AskUsMailer $askUsMailer,
        TranslatorInterface $translator
    ) {
        $this->requestParser = $requestParser;
        $this->handler = $handler;
        $this->exceptionView = $exceptionView;
        $this->askUsMailer = $askUsMailer;
        $this->translator = $translator;
    }

    
    #[Route(path: '/api/ask-us', name: 'site_api.ask_us', options: ['expose' => true], methods: ['POST'])]
    public function insert(AskUsRequestDto $askUsRequestDto): JsonResponse
    {
        if (false === $this->isCsrfTokenValid('ask_us_form', $askUsRequestDto->csrf)) {
            $this->createAccessDeniedException();
        }

        try {
            $askUs = $this->requestParser->parse($askUsRequestDto);

            $this->handler->save($askUs);

            $this->askUsMailer->sendEmail($askUs, $askUsRequestDto->locale);

        } catch (\Throwable $throwable) {
            return $this->json(
                ['error' => $this->exceptionView->view($throwable, $askUsRequestDto->locale)],
                Response::HTTP_BAD_REQUEST
            );
        }

        return $this->json(
            ['message' => $this->translator->trans('ask_us.message.success', [], 'messages', $askUsRequestDto->locale)],
            Response::HTTP_CREATED
        );
    }
}
