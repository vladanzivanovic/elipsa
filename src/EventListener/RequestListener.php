<?php

declare(strict_types=1);

namespace App\EventListener;

use Symfony\Component\HttpKernel\Event\RequestEvent;

final class RequestListener
{
    public function onKernelRequest(RequestEvent $event)
    {
        $session = $event->getRequest()->getSession();

        if (false === $session->has('user')) {
            $session->set('user', null);
        }
    }
}