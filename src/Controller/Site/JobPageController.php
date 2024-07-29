<?php

declare(strict_types=1);

namespace App\Controller\Site;

use App\Entity\CareerDescriptionTranslation;
use App\Formatter\Site\JobPageResponseFormatter;
use App\Repository\CareerDescriptionRepository;
use Symfony\Bridge\Twig\Attribute\Template;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;

final class JobPageController extends AbstractController
{
    private \App\Repository\CareerDescriptionRepository $repository;

    private \App\Formatter\Site\JobPageResponseFormatter $responseFormatter;

    public function __construct(
        CareerDescriptionRepository $repository,
        JobPageResponseFormatter $responseFormatter
    ) {
        $this->repository = $repository;
        $this->responseFormatter = $responseFormatter;
    }

    #[Route(path: ['rs' => '/radna-mesta', 'en' => '/jobs'], name: 'site.jobs_list_page', options: ['expose' => true], methods: ['GET'])]
    #[Template('Site/Pages/career.html.twig')]
    public function index(Request $request): array
    {
        $locale = $request->getSession()->get('_locale');
        $data = $this->repository->getActiveList($locale);

        return $this->responseFormatter->formatResponse($data);
    }

    
    #[Route(path: ['rs' => '/radna-mesta/{slug}', 'en' => '/jobs/{slug}'], name: 'site.jobs_detail_page', methods: ['GET'], options: ['expose' => true])]
    #[Template('Site/Pages/jobPage.html.twig')]
    public function detailPage(CareerDescriptionTranslation $careerDescriptionTranslation, Request $request): array
    {
        $locale = $request->getSession()->get('_locale');
        $this->repository->getActiveList($locale);

        return [
            'job' => $careerDescriptionTranslation->getCareerDescription(),
            'trans' => $careerDescriptionTranslation,
        ];
    }
}
