<?php

declare(strict_types=1);

namespace App\Parser\Site;

use App\Entity\Notification;
use App\Exception\NotificationException;
use App\Repository\NotificationRepository;
use App\Request\Dto\NotificationRequestDto;
use Doctrine\ORM\NonUniqueResultException;

final class NotificationRequestParser
{
    private NotificationRepository $notificationRepository;

    private array $checkers;

    public function __construct(
        NotificationRepository $notificationRepository,
        iterable $checkers
    ) {
        $this->notificationRepository = $notificationRepository;
        $this->checkers = iterator_to_array($checkers);
    }

    /**
     * @throws NotificationException
     * @throws NonUniqueResultException
     */
    public function parse(
        NotificationRequestDto $notificationRequestDto,
        string $locale
    ): Notification {

        if (false === $this->checkPayload($notificationRequestDto)) {
            throw new NotificationException('notification.size_is_available');
        }

        $notification = $this->notificationRepository->getByValues(
            $notificationRequestDto->type,
            $notificationRequestDto->email,
            $notificationRequestDto->payload
        );

        if ($notification instanceof Notification) {
            throw new NotificationException('notification.already_set_notification');
        }

        $notification = $this->create();
        $notification->setEmail($notificationRequestDto->email);
        $notification->setType($notificationRequestDto->type);
        $notification->setPayload($notificationRequestDto->payload);
        $notification->setLocale($locale);
        $notification->setCountry($notificationRequestDto->country);
        return $notification;
    }

    public function create(): Notification
    {
        return new Notification();
    }

    private function checkPayload(NotificationRequestDto $notificationRequestDto): bool
    {
        foreach ($this->checkers as $checker) {
            if ($notificationRequestDto->type === $checker->getType()) {
                return $checker->isNotifyEligible($notificationRequestDto);
            }
        }
    }
}
