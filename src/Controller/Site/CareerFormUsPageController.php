<?php

declare(strict_types=1);

namespace App\Controller\Site;

use App\Repository\CareerDescriptionRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

final class CareerFormUsPageController extends AbstractController
{
    /**
     * @var CareerDescriptionRepository
     */
    private $descriptionRepository;

    /**
     * @param CareerDescriptionRepository $descriptionRepository
     */
    public function __construct(
        CareerDescriptionRepository $descriptionRepository
    ) {
        $this->descriptionRepository = $descriptionRepository;
    }

    /**
     * @Route({
     *          "rs": "/radna-mesta/prijava",
     *          "en": "/careers/apply"
     *     },
     *     name="site.career_page",
     *     methods={"GET"}
     * )
     * @Template("Site/Pages/careerForm.html.twig")
     *
     * @param Request $request
     *
     * @return string[]
     */
    public function renderPage(Request $request)
    {
        $options = $this->descriptionRepository->getForOptions($request->getSession()->get('_locale'));

        return [
            'jobs' => $options,
        ];
    }
}