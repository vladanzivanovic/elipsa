<?php

declare(strict_types=1);

namespace App\Security;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\Exception\CustomUserMessageAuthenticationException;
use Symfony\Component\Security\Core\Exception\InvalidCsrfTokenException;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Csrf\CsrfToken;
use Symfony\Component\Security\Csrf\CsrfTokenManagerInterface;
use Symfony\Component\Security\Guard\Authenticator\AbstractFormLoginAuthenticator;
use Symfony\Component\Security\Guard\PasswordAuthenticatedInterface;
use Symfony\Component\Security\Http\Util\TargetPathTrait;
use Symfony\Contracts\Translation\TranslatorInterface;

class LoginFormAuthenticator extends AbstractFormLoginAuthenticator implements PasswordAuthenticatedInterface
{
    use TargetPathTrait;

    public const LOGIN_ROUTE_SITE = 'site_api.login';
    public const LOGIN_ROUTE_ADMIN = 'admin_api.login';

    public const SUPPORTED_LOGIN_ROUTES = [
        self::LOGIN_ROUTE_SITE,
        self::LOGIN_ROUTE_ADMIN,
    ];

    private $entityManager;
    private $urlGenerator;
    private $csrfTokenManager;
    private $passwordEncoder;

    /**
     * @var TokenStorageInterface
     */
    private $tokenStorage;
    /**
     * @var TranslatorInterface
     */
    private $translator;
    /**
     * @var RouterInterface
     */
    private $router;

    /**
     * @param EntityManagerInterface       $entityManager
     * @param UrlGeneratorInterface        $urlGenerator
     * @param CsrfTokenManagerInterface    $csrfTokenManager
     * @param UserPasswordEncoderInterface $passwordEncoder
     * @param TokenStorageInterface        $tokenStorage
     * @param TranslatorInterface          $translator
     * @param RouterInterface              $router
     */
    public function __construct(
        EntityManagerInterface $entityManager,
        UrlGeneratorInterface $urlGenerator,
        CsrfTokenManagerInterface $csrfTokenManager,
        UserPasswordEncoderInterface $passwordEncoder,
        TokenStorageInterface $tokenStorage,
        TranslatorInterface $translator,
        RouterInterface $router
    ) {
        $this->entityManager = $entityManager;
        $this->urlGenerator = $urlGenerator;
        $this->csrfTokenManager = $csrfTokenManager;
        $this->passwordEncoder = $passwordEncoder;
        $this->tokenStorage = $tokenStorage;
        $this->translator = $translator;
        $this->router = $router;
    }

    public function supports(Request $request)
    {
        return in_array($request->attributes->get('_route'), self::SUPPORTED_LOGIN_ROUTES)
            && $request->isMethod('POST');
    }

    public function getCredentials(Request $request)
    {
        $credentials = [
            'email' => $request->request->get('username'),
            'password' => $request->request->get('password'),
            'csrf_token' => $request->request->get('_csrf_token'),
        ];

        $credentials = array_filter($credentials);

        if (empty($credentials)) {
            return null;
        }

        $request->getSession()->set(
            Security::LAST_USERNAME,
            $credentials['email']
        );

        return $credentials;
    }

    public function getUser($credentials, UserProviderInterface $userProvider)
    {
        $token = new CsrfToken('authenticate', $credentials['csrf_token']);
        if (!$this->csrfTokenManager->isTokenValid($token)) {

            throw new InvalidCsrfTokenException();
        }

        $user = $this->entityManager->getRepository(User::class)->findOneBy(['email' => $credentials['email']]);

        if (!$user) {
            // fail authentication with a custom error
            throw new CustomUserMessageAuthenticationException('Email could not be found.');
        }

        return $user;
    }

    public function checkCredentials($credentials, UserInterface $user)
    {
        return $this->passwordEncoder->isPasswordValid($user, $credentials['password']);
    }

    /**
     * Used to upgrade (rehash) the user's password automatically over time.
     */
    public function getPassword($credentials): ?string
    {
        return $credentials['password'];
    }

    public function onAuthenticationSuccess(Request $request, TokenInterface $token, $providerKey)
    {
        if ( false === $request->isXmlHttpRequest() ) {
            $this->tokenStorage->setToken(null);
            $request->getSession()->invalidate();

            throw new BadRequestHttpException('Login is allowed only through POST method.');
        }

        /** @var User $user */
        $user = $token->getUser();

        if(User::STATUS_PENDING === $user->getStatus()){
            $this->tokenStorage->setToken(null);
            $request->getSession()->invalidate();

            return new JsonResponse(null, JsonResponse::HTTP_BAD_REQUEST);
        }

        $request->getSession()->getFlashBag()->add('message', $this->translator->trans('login.successful'));

        return new JsonResponse(null);
    }

    /**
     * @param Request                 $request
     * @param AuthenticationException $exception
     *
     * @return JsonResponse|RedirectResponse|Response
     */
    public function onAuthenticationFailure(Request $request, AuthenticationException $exception)
    {
        dd($exception);
        if ( $request->isXmlHttpRequest() ) {
            $message = $this->translator->trans($exception->getMessage());
            $array = array('message' => $message );

            return new JsonResponse($array, JsonResponse::HTTP_BAD_REQUEST);
        }

        throw new BadRequestHttpException('Login is allowed only through POST method.');
    }

    /**
     * @param Request                      $request
     * @param AuthenticationException|null $authException
     *
     * @return RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function start(Request $request, AuthenticationException $authException = null): RedirectResponse
    {
        if (null !== $authException) {
            if (false !== strpos($request->attributes->get('_route'), 'admin')) {
                return new RedirectResponse($this->router->generate('admin.login'));
            }

            return new RedirectResponse($this->router->generate('site.home_page', ['_locale' => $request->getSession()->get('_locale')]));
        }

    }

    protected function getLoginUrl()
    {
        return $this->urlGenerator->generate(self::LOGIN_ROUTE_SITE);
    }
}
