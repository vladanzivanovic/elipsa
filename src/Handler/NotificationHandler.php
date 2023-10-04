<?php

declare(strict_types=1);

namespace App\Handler;

use App\Entity\Notification;
use App\Helper\ValidatorHelper;
use App\Repository\NotificationRepository;
use Symfony\Component\HttpKernel\Exception\UnprocessableEntityHttpException;

final class NotificationHandler
{
    private ValidatorHelper $validator;

    private NotificationRepository $notificationRepository;

    public function __construct(
        ValidatorHelper $validator,
        NotificationRepository $notificationRepository
    ) {
        $this->validator = $validator;
        $this->notificationRepository = $notificationRepository;
    }

    /**
     * @throws \Exception
     */
    public function save(Notification $notification, string $group = null): void
    {
        $errors = $this->validator->validate($notification, null, $group);

        if ($errors->count() > 0) {
            throw new UnprocessableEntityHttpException(json_encode($this->validator->parseErrors($errors)));
        }

        $this->notificationRepository->persist($notification);

        $this->notificationRepository->flush();
    }

    public function sendNotificationEmails(
        Notification $notification,
        \Closure $mailerClosure
    ): void {
        $mailerClosure($notification);

        $this->notificationRepository->removeWithFlush($notification);
    }
}
