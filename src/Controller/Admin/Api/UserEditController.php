<?php

declare(strict_types=1);

namespace App\Controller\Admin\Api;

use App\Entity\Banner;
use App\Entity\User;
use App\Handler\Site\UserHandler;
use App\Helper\ConstantsHelper;
use App\Parser\UserEditRequestParser;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;

final class UserEditController extends AbstractController
{
    /**
     * @var UserEditRequestParser
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
     * @param UserEditRequestParser $requestParser
     * @param UserHandler           $handler
     * @param TranslatorInterface   $translator
     */
    public function __construct(
        UserEditRequestParser $requestParser,
        UserHandler $handler,
        TranslatorInterface $translator
    ) {
        $this->requestParser = $requestParser;
        $this->handler = $handler;
        $this->translator = $translator;
    }

    /**
     * @Route("/api/add-user",
     *     name="admin.add_user_api",
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
        $bag = $request->request;

        if (false === $this->isCsrfTokenValid('set_user', $csrf)) {
            $this->createAccessDeniedException();
        }

        try {
            $user = $this->requestParser->parse($bag);

            if (null !== $bag->get('address')) {
                $this->requestParser->parseAddress($bag, $user);
            }

            $this->handler->save($user, 'SetUserAdmin', true);
            $request->getSession()->getFlashBag()->add('message', $this->translator->trans('my_account.personal_info.success.message'));

        } catch (BadRequestHttpException $httpException) {
            return $this->json(['error' => $httpException->getMessage()], Response::HTTP_BAD_REQUEST);
        }

        return $this->json(null, Response::HTTP_CREATED);
    }

    /**
     * @Route("/api/update-user/{id}",
     *     name="admin.edit_user_api",
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

        if (false === $this->isCsrfTokenValid('set_user', $csrf)) {
            $this->createAccessDeniedException();
        }

        try {
            $user = $this->requestParser->parse($bag, $user);

            if (null !== $bag->get('address')) {
                $this->requestParser->parseAddress($bag, $user);
            }

            $this->handler->save($user, 'UpdateUser', $bag->get('password') !== null);
            $request->getSession()->getFlashBag()->add('message', $this->translator->trans('my_account.personal_info.success.message'));

        } catch (BadRequestHttpException $httpException) {
            return $this->json(['error' => $httpException->getMessage()], Response::HTTP_BAD_REQUEST);
        }

        return $this->json(null, Response::HTTP_CREATED);
    }

    /**
     * @Route("/api/toggle-user-status/{id}/{status}", name="admin.api_toggle_user_status", methods={"PATCH"},
     *                                                   options={"expose": true})
     *
     * @param User $user
     * @param int  $status
     *
     * @return JsonResponse
     *
     */
    public function toggleActivation(User $user, int $status): JsonResponse
    {
        $user->setStatus((int) $status);

        $this->handler->save($user, 'UpdateUser');

        $statusText = ConstantsHelper::getConstantName((string) $status, 'STATUS', User::class);

        return $this->json(['text' => $statusText]);
    }

    /**
     * @Route("/api/disable-user/{id}", name="admin.disable_user_api", methods={"DELETE"}, options={"expose": true})
     *
     * @param User $user
     *
     * @return JsonResponse
     */
    public function remove(User $user): JsonResponse
    {
        $user->setStatus(User::STATUS_DISABLED);

        $this->handler->save($user, 'UpdateUser');


        return $this->json(['text' => $statusText]);
    }
}
