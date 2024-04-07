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
    private \App\Parser\UserEditRequestParser $requestParser;

    private \App\Handler\Site\UserHandler $handler;

    private \Symfony\Contracts\Translation\TranslatorInterface $translator;

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
     *
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     * @throws \ReflectionException
     */
    #[Route(path: '/api/add-user', name: 'admin.add_user_api', methods: ['POST'], options: ['expose' => true])]
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

            $this->handler->save($user, 'SetUserAdmin');
            $request->getSession()->getFlashBag()->add('message', $this->translator->trans('my_account.personal_info.success.message'));

        } catch (BadRequestHttpException $httpException) {
            return $this->json(['error' => $httpException->getMessage()], Response::HTTP_BAD_REQUEST);
        }

        return $this->json(null, Response::HTTP_CREATED);
    }

    /**
     *
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    #[Route(path: '/api/update-user/{id}', name: 'admin.edit_user_api', methods: ['PUT'], options: ['expose' => true])]
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

            $this->handler->save($user, 'UpdateUser');
            $request->getSession()->getFlashBag()->add('message', $this->translator->trans('my_account.personal_info.success.message'));

        } catch (BadRequestHttpException $httpException) {
            return $this->json(['error' => $httpException->getMessage()], Response::HTTP_BAD_REQUEST);
        }

        return $this->json(null, Response::HTTP_CREATED);
    }

    
    #[Route(path: '/api/toggle-user-status/{id}/{status}', name: 'admin.api_toggle_user_status', methods: ['PATCH'], options: ['expose' => true])]
    public function toggleActivation(User $user, int $status): JsonResponse
    {
        $user->setStatus($status);

        $this->handler->save($user, 'UpdateUser');

        $statusText = ConstantsHelper::getConstantName((string) $status, 'STATUS', User::class);

        return $this->json(['text' => $statusText]);
    }

    
    #[Route(path: '/api/disable-user/{id}', name: 'admin.disable_user_api', methods: ['DELETE'], options: ['expose' => true])]
    public function remove(User $user): JsonResponse
    {
        $user->setStatus(User::STATUS_DISABLED);

        $this->handler->save($user, 'UpdateUser');


        return $this->json(['text' => $statusText]);
    }
}
