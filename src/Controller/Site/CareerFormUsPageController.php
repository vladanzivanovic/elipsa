<?php

declare(strict_types=1);

namespace App\Controller\Site;

use App\Repository\CareerDescriptionRepository;
use Symfony\Bridge\Twig\Attribute\Template;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

final class CareerFormUsPageController extends AbstractController
{
    private \App\Repository\CareerDescriptionRepository $descriptionRepository;

    public function __construct(
        CareerDescriptionRepository $descriptionRepository
    ) {
        $this->descriptionRepository = $descriptionRepository;
    }

    #[Route(path: ['rs' => '/radna-mesta/prijava', 'en' => '/careers/apply'], name: 'site.career_page', methods: ['GET'])]
    #[Template('Site/Pages/careerForm.html.twig')]
    public function renderPage(Request $request): array
    {
        $options = $this->descriptionRepository->getForOptions($request->getSession()->get('_locale'));

        return [
            'jobs' => $options,
        ];
    }
}
