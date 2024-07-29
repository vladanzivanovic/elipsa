<?php

declare(strict_types=1);

namespace App\Controller\Admin;

use App\Entity\Banner;
use App\Entity\User;
use App\Formatter\Admin\BannerEditResponseFormatter;
use App\Formatter\Admin\UserEditResponseFormatter;
use Symfony\Bridge\Twig\Attribute\Template;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\Routing\Attribute\Route;

final class UserEditPageController extends AbstractController
{
    private \App\Formatter\Admin\UserEditResponseFormatter $responseFormatter;

    public function __construct(
        ParameterBagInterface $bag,
        UserEditResponseFormatter $responseFormatter
    ) {
        $this->responseFormatter = $responseFormatter;
    }

    #[Route(path: '/add-user', name: 'admin.add_user_page', methods: ['GET'])]
    #[Template('Admin/Pages/userEdit.html.twig')]
    public function insert(): array
    {
        return [];
    }

    
    #[Route(path: '/edit-user/{id}', name: 'admin.edit_user_page', methods: ['GET'])]
    #[Template('Admin/Pages/userEdit.html.twig')]
    public function update(User $user): array
    {
        return $this->responseFormatter->formatResponse($user);
    }
}
