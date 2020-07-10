<?php

declare(strict_types=1);

namespace App\Controller\Admin;

use App\Entity\Banner;
use App\Formatter\Admin\DescriptionEditResponseFormatter;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\Routing\Annotation\Route;

final class DescriptionEditPageController extends AbstractController
{
    /**
     * @var DescriptionEditResponseFormatter
     */
    private $responseFormatter;
    /**
     * @var ParameterBagInterface
     */
    private $bag;

    /**
     * @param DescriptionEditResponseFormatter $responseFormatter
     * @param ParameterBagInterface            $bag
     */
    public function __construct(
        DescriptionEditResponseFormatter $responseFormatter,
        ParameterBagInterface $bag
    ) {
        $this->responseFormatter = $responseFormatter;
        $this->bag = $bag;
    }

    /**
     * @Route("/add-description", name="admin.add_description_page", methods={"GET"})
     * @Template("Admin/Pages/descriptionEdit.html.twig")
     *
     * @return array
     */
    public function insert(): array
    {
        return [];
    }

    /**
     * @Route("/edit-description/{type}", name="admin.edit_description_page", methods={"GET"})
     * @Template("Admin/Pages/descriptionEdit.html.twig")
     *
     * @param int $type
     *
     * @return array
     */
    public function update(int $type): array
    {
        return $this->responseFormatter->formatResponse($type);
    }
}