<?php

declare(strict_types=1);

namespace App\Controller\Admin\Api;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

final class SecurityController extends AbstractController
{
    /**
     * @Route({
     *          "rs": "/api/login",
     *          "en": "/api/login"
     *      },
     *     name="admin_api.login",
     *     methods={"POST"},
     *     options={"expose": true}
     * )
     * @param Request $request
     *
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     */
    public function login(Request $request)
    {
        $user = $this->getUser();

        return $this->json([
            'username' => $user->getUsername(),
            'roles' => $user->getRoles(),
        ]);
    }
}