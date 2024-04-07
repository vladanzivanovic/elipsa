<?php

declare(strict_types=1);

namespace App\Controller\Site\Api;

use App\Entity\User;
use App\Exception\UserException;
use App\Handler\Site\UserHandler;
use App\Parser\Site\UserEditRequestParser;
use App\Request\Dto\RegistrationRequestDto;
use App\Request\Dto\UserRequestDto;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;

final class UserEditController extends AbstractController
{
    private UserEditRequestParser $requestParser;

    private UserHandler $handler;

    private TranslatorInterface $translator;

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
     * @throws ORMException
     * @throws OptimisticLockException|UserException
     */
    #[Route(path: '/api/user/{id}', name: 'site_api.user.update', methods: ['PUT'], options: ['expose' => true])]
    public function update(UserRequestDto $userRequestDto, User $user, Request $request): JsonResponse
    {
        $csrf = $userRequestDto->csrf;

        if (false === $this->isCsrfTokenValid('user_update', $csrf)) {
            $this->createAccessDeniedException();
        }

        try {
            $user = $this->requestParser->parse($userRequestDto, $user);

            $this->handler->save($user, 'UpdateUser');
            $request->getSession()->getFlashBag()->add('message', $this->translator->trans('my_account.personal_info.success.message'));

        } catch (BadRequestHttpException $httpException) {
            return $this->json(['error' => $httpException->getMessage()], Response::HTTP_BAD_REQUEST);
        }

        return $this->json(null, Response::HTTP_CREATED);
    }
}
