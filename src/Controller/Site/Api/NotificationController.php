<?php

declare(strict_types=1);

namespace App\Controller\Site\Api;

use App\Handler\NotificationHandler;
use App\Parser\Site\NotificationRequestParser;
use App\Request\Dto\NotificationRequestDto;
use App\View\ExceptionView;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

final class NotificationController extends AbstractController
{
    private NotificationRequestParser $notificationRequestParser;

    private NotificationHandler $notificationHandler;

    private ExceptionView $exceptionView;

    public function __construct(
        NotificationRequestParser $notificationRequestParser,
        NotificationHandler $notificationHandler,
        ExceptionView $exceptionView
    ) {
        $this->notificationRequestParser = $notificationRequestParser;
        $this->notificationHandler = $notificationHandler;
        $this->exceptionView = $exceptionView;
    }

    /**
     * @Route("/api/notification", name="site_api.set_notification", methods={"POST"}, options={"expose": true})
     *
     * @return JsonResponse
     */
    public function setNotification(
        NotificationRequestDto $notificationRequestDto,
        Request $request
    ): JsonResponse {
        try {
            $notification = $this->notificationRequestParser->parse($notificationRequestDto, $request->getLocale());

            $this->notificationHandler->save($notification);

            return $this->json(null, Response::HTTP_CREATED);
        } catch (\Throwable $exception) {
            return $this->json(
                ['error' => $this->exceptionView->view($exception, $request->getLocale())],
                Response::HTTP_BAD_REQUEST
            );
        }
    }
}
