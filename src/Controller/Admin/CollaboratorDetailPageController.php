<?php

declare(strict_types=1);

namespace App\Controller\Admin;

use App\Entity\Career;
use App\Entity\Collaborator;
use App\Entity\ShopOrder;
use App\Formatter\Admin\CareerDetailResponseFormatter;
use App\Formatter\Admin\CollaboratorDetailResponseFormatter;
use Symfony\Bridge\Twig\Attribute\Template;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\Routing\Attribute\Route;

final class CollaboratorDetailPageController extends AbstractController
{
    private \App\Formatter\Admin\CollaboratorDetailResponseFormatter $responseFormatter;

    public function __construct(
        ParameterBagInterface $bag,
        CollaboratorDetailResponseFormatter $responseFormatter
    ) {
        $this->responseFormatter = $responseFormatter;
    }

    /**
     *
     * @throws \ReflectionException
     */
    #[Route(path: '/collaborator-detail/{id}', name: 'admin.view_collaborator_details', methods: ['GET'], options: ['expose' => true])]
    #[Template('Admin/Pages/collaboratorDetail.html.twig')]
    public function renderPage(Collaborator $collaborator): array
    {
        return $this->responseFormatter->formatResponse($collaborator);
    }
}
