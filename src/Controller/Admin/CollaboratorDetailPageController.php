<?php

declare(strict_types=1);

namespace App\Controller\Admin;

use App\Entity\Career;
use App\Entity\Collaborator;
use App\Entity\ShopOrder;
use App\Formatter\Admin\CareerDetailResponseFormatter;
use App\Formatter\Admin\CollaboratorDetailResponseFormatter;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\Routing\Annotation\Route;

final class CollaboratorDetailPageController extends AbstractController
{
    /**
     * @var CollaboratorDetailResponseFormatter
     */
    private $responseFormatter;
    /**
     * @var ParameterBagInterface
     */
    private $bag;

    /**
     * @param ParameterBagInterface               $bag
     * @param CollaboratorDetailResponseFormatter $responseFormatter
     */
    public function __construct(
        ParameterBagInterface $bag,
        CollaboratorDetailResponseFormatter $responseFormatter
    ) {
        $this->responseFormatter = $responseFormatter;
        $this->bag = $bag;
    }

    /**
     * @Route("/collaborator-detail/{id}", name="admin.view_collaborator_details", methods={"GET"}, options={"expose": true})
     * @Template("Admin/Pages/collaboratorDetail.html.twig")
     *
     * @param Collaborator $collaborator
     *
     * @return array
     * @throws \ReflectionException
     */
    public function renderPage(Collaborator $collaborator): array
    {
        return $this->responseFormatter->formatResponse($collaborator);
    }
}