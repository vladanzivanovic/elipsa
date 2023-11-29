<?php

declare(strict_types=1);

namespace App\EventListener;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\KernelEvents;

class LocaleSubscriber implements EventSubscriberInterface
{
    private $defaultLocale;

    public function __construct($defaultLocale = 'rs')
    {
        $this->defaultLocale = $defaultLocale;
    }

    public function onKernelRequest(RequestEvent $event)
    {
        $request = $event->getRequest();

        if ($request->isXmlHttpRequest()) {
            if ($request->headers->has('Content-Language')) {
                $locale = $request->headers->get('Content-Language');

                $request->setLocale($locale);
            }

            return;
        }

        // try to see if the locale has been set as a _locale routing parameter
        if ($locale = $request->attributes->get('_locale')) {
            $request->getSession()->set('_locale', $locale);
            $request->setLocale($request->getSession()->get('_locale'));

            return;
        }
        // if no explicit locale has been set on this request, use one from the session
        $request->setLocale($this->defaultLocale);
        $request->getSession()->set('_locale', $this->defaultLocale);
    }

    public static function getSubscribedEvents(): array
    {
        return [
            // must be registered before (i.e. with a higher priority than) the default Locale listener
            KernelEvents::REQUEST => [['onKernelRequest', 0]],
        ];
    }
}
