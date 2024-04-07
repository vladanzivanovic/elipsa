<?php

declare(strict_types=1);

namespace App\EventListener;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\KernelEvents;

final class RequestEventListener
{
    public function onKernelRequest(RequestEvent $event): void
    {
        $requestUri = $event->getRequest()->server->get('REQUEST_URI');

        if (strpos($requestUri, '/public') === 0) {

            $redirectUri = str_replace('/public', '', $requestUri);

            $event->setResponse(new RedirectResponse($redirectUri !== '' ? $redirectUri : '/', Response::HTTP_MOVED_PERMANENTLY));

            return;
        }

        $session = $event->getRequest()->getSession();

        if (false === $session->has('user')) {
            $session->set('user', null);
        }
    }
}
