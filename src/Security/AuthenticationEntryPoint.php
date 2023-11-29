<?php

declare(strict_types=1);

namespace App\Security;

use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Http\EntryPoint\AuthenticationEntryPointInterface;

class AuthenticationEntryPoint implements AuthenticationEntryPointInterface
{
    private RouterInterface $router;

    public function __construct(
        RouterInterface $router
    ) {
        $this->router = $router;
    }

    public function start(Request $request, AuthenticationException $authException = null): RedirectResponse
    {
        if (false !== strpos($request->attributes->get('_route'), 'admin')) {
            return new RedirectResponse($this->router->generate('admin.login'));
        }

        return new RedirectResponse($this->router->generate('site.home_page', ['_locale' => $request->getSession()->get('_locale')]));
    }
}
