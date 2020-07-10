<?php

declare(strict_types=1);

namespace App\Controller\Site\Api;

use App\Entity\User;
use App\Handler\Site\LoyaltyHandler;
use App\Handler\Site\UserHandler;
use App\Parser\Site\RegistrationRequestParser;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;

final class UserEditController extends AbstractController
{
    /**
     * @var RegistrationRequestParser
     */
    private $requestParser;

    /**
     * @var UserHandler
     */
    private $handler;

    /**
     * @var TranslatorInterface
     */
    private $translator;

    /**
     * @param RegistrationRequestParser $requestParser
     * @param UserHandler               $handler
     * @param TranslatorInterface       $translator
     */
    public function __construct(
        RegistrationRequestParser $requestParser,
        UserHandler $handler,
        TranslatorInterface $translator
    ) {
        $this->requestParser = $requestParser;
        $this->handler = $handler;
        $this->translator = $translator;
    }

    /**
     * @Route({
     *          "rs": "/api/add-user",
     *          "en": "/api/add-user"
     *      },
     *     name="site_api.user_registration",
     *     methods={"POST"},
     *     options={"expose": true}
     * )
     * @param Request $request
     *
     * @return JsonResponse
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     * @throws \ReflectionException
     */
    public function add(Request $request): JsonResponse
    {
        $csrf = $request->request->get('_csrf_token');

        if (false === $this->isCsrfTokenValid('user_registration', $csrf)) {
            $this->createAccessDeniedException();
        }

        try {
            $user = $this->requestParser->parse($request->request);

            $this->handler->save($user, $request->getLocale(), 'SetUser', true, true);
            $request->getSession()->getFlashBag()->add('message', $this->translator->trans('registration.success.message'));

        } catch (BadRequestHttpException $httpException) {
            return $this->json(['error' => $httpException->getMessage()], JsonResponse::HTTP_BAD_REQUEST);
        }

        return $this->json(null, JsonResponse::HTTP_CREATED);
    }

    /**
     * @Route({
     *          "rs": "/api/update-user/{id}",
     *          "en": "/api/update-user/{id}"
     *      },
     *     name="site_api.user_update",
     *     methods={"PUT"},
     *     options={"expose": true}
     * )
     * @param Request $request
     * @param User    $user
     *
     * @return JsonResponse
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function update(Request $request, User $user): JsonResponse
    {
        $csrf = $request->request->get('_csrf_token');
        $bag = $request->request;

        if (false === $this->isCsrfTokenValid('user_update', $csrf)) {
            $this->createAccessDeniedException();
        }

        try {
            $user = $this->requestParser->parse($bag, $user);

            if (null !== $bag->get('address')) {
                $this->requestParser->parseAddress($bag, $user);
            }

            $this->handler->save($user, $request->getLocale(), 'UpdateUser', false, $bag->get('password') !== null);
            $request->getSession()->getFlashBag()->add('message', $this->translator->trans('my_account.personal_info.success.message'));

        } catch (BadRequestHttpException $httpException) {
            return $this->json(['error' => $httpException->getMessage()], JsonResponse::HTTP_BAD_REQUEST);
        }

        return $this->json(null, JsonResponse::HTTP_CREATED);
    }
}