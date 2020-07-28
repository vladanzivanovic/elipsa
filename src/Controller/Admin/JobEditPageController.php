<?php

declare(strict_types=1);

namespace App\Controller\Admin;

use App\Entity\Banner;
use App\Entity\CareerDescription;
use App\Formatter\Admin\JobEditResponseFormatter;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\Routing\Annotation\Route;

final class JobEditPageController extends AbstractController
{
    /**
     * @var JobEditResponseFormatter
     */
    private $responseFormatter;

    /**
     * @var ParameterBagInterface
     */
    private $bag;

    /**
     * @param ParameterBagInterface    $bag
     * @param JobEditResponseFormatter $responseFormatter
     */
    public function __construct(
        ParameterBagInterface $bag,
        JobEditResponseFormatter $responseFormatter
    ) {
        $this->responseFormatter = $responseFormatter;
        $this->bag = $bag;
    }

    /**
     * @Route("/add-job", name="admin.add_job", methods={"GET"})
     * @Template("Admin/Pages/jobEdit.html.twig")
     *
     * @return array
     */
    public function insert(): array
    {
        return [];
    }

    /**
     * @Route("/edit-job/{id}", name="admin.edit_job", methods={"GET"})
     * @Template("Admin/Pages/jobEdit.html.twig")
     *
     * @param CareerDescription $careerDescription
     *
     * @return array
     */
    public function update(CareerDescription $careerDescription): array
    {
        return $this->responseFormatter->formatResponse($careerDescription);
    }
}