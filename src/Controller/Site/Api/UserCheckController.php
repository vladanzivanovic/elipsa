<?php

declare(strict_types=1);

namespace App\Controller\Site\Api;

use App\Entity\User;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Serializer\Encoder\JsonEncode;

/**
 * @deprecated
 */
final class UserCheckController extends AbstractController
{
    private \App\Repository\UserRepository $userRepository;

    public function __construct(
        UserRepository $userRepository
    ) {

        $this->userRepository = $userRepository;
    }

    /**
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    #[Route(path: '/user-exists/{email}', name: 'site_api.user_exists', methods: ['GET'], options: ['expose' => true])]
    public function userExistsByEmail(string $email): JsonResponse
    {
        $user = $this->userRepository->getByEmail($email);

        if (null !== $user) {
            if ($user->getStatus() === User::STATUS_DISABLED) {
                return $this->json([], JsonResponse::HTTP_FORBIDDEN);
            }

            if ($user->getStatus() === User::STATUS_PENDING) {
                return $this->json([]);
            }

            return $this->json([], JsonResponse::HTTP_UNPROCESSABLE_ENTITY);
        }

        return $this->json([]);
    }
}
