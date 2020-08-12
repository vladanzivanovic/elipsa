<?php

declare(strict_types=1);

namespace App\Controller\Site\Api;

use App\Entity\User;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\Encoder\JsonEncode;

final class UserCheckController extends AbstractController
{
    /**
     * @var UserRepository
     */
    private $userRepository;

    /**
     * @param UserRepository $userRepository
     */
    public function __construct(
        UserRepository $userRepository
    ) {

        $this->userRepository = $userRepository;
    }

    /**
     * @Route("/user-exists/{email}", name="site_api.user_exists", methods={"GET"}, options={"expose": true})
     * @param string $email
     *
     * @return JsonResponse
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
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