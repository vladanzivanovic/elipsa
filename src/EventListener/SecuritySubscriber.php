<?php

declare(strict_types=1);

namespace App\EventListener;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Exception\BadCredentialsException;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\RememberMeBadge;
use Symfony\Component\Security\Http\Event\LoginFailureEvent;
use Symfony\Component\Security\Http\Event\LoginSuccessEvent;
use Symfony\Component\Security\Http\ParameterBagUtils;
use Symfony\Contracts\Translation\TranslatorInterface;

final class SecuritySubscriber implements EventSubscriberInterface
{
    private TranslatorInterface $translator;

    public function __construct(
        TranslatorInterface $translator
    ){
        $this->translator = $translator;
    }

    public static function getSubscribedEvents(): array
    {
        return [
            LoginSuccessEvent::class => ['onSuccessfulLogin'],
            LoginFailureEvent::class => 'loginFailure',
        ];
    }

    public function onSuccessfulLogin(LoginSuccessEvent $event): void
    {
        $this->setRememberMeBadge($event);
    }

    public function loginFailure(LoginFailureEvent $event)
    {
        if ($event->getException() instanceof BadCredentialsException) {
            /** @var JsonResponse $response */
            $response = $event->getResponse();

            $response->setJson(json_encode([
                'message' => $this->translator->trans(
                    'user.invalid_credentials',
                    [],
                    'security',
                    $event->getRequest()->headers->get('Content-Language')
                )
            ]));
        }
    }

    private function setRememberMeBadge(LoginSuccessEvent $event): void
    {
        $request = $event->getRequest();

        $body = json_decode($request->getContent(), true);

        $event->getPassport()->addBadge(new RememberMeBadge());

        $request->attributes->set('_remember_me', isset($body['_remember_me']));
    }
}
