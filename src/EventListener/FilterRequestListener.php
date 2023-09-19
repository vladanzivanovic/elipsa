<?php

declare(strict_types=1);

namespace App\EventListener;

use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Contracts\Translation\TranslatorInterface;

final class FilterRequestListener
{
    private TranslatorInterface $translator;

    public function __construct(
        TranslatorInterface $translator
    ) {
        $this->translator = $translator;
    }

    public function onKernelRequest(RequestEvent $event)
    {
        $request = $event->getRequest();

        if ('site.shop_page' === $request->attributes->get('_route')) {
            if (0 < $request->query->count()) {
                foreach ($request->query->all() as $name => $value) {
                    $request->query->remove($name);

                    $request->query->set(
                        $this->translator->trans('filter.'.$name, [], 'messages', $request->getLocale()),
                        $value
                    );
                }
            }
        }
    }
}
