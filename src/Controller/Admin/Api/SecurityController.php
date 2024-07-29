<?php

declare(strict_types=1);

namespace App\Controller\Admin\Api;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;

final class SecurityController extends AbstractController
{
    /**
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     */
    #[Route(path: ['rs' => '/api/login', 'en' => '/api/login'], name: 'admin_api.login', methods: ['POST'], options: ['expose' => true])]
    public function login()
    {
        $user = $this->getUser();
        return $this->json([
            'username' => $user->getUserIdentifier(),
            'roles' => $user->getRoles(),
        ]);
    }
}
