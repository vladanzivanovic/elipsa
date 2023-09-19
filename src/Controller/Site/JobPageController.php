<?php

declare(strict_types=1);

namespace App\Controller\Site;

use App\Collector\ShopPageCollector;
use App\Entity\CareerDescriptionTranslation;
use App\Formatter\Site\JobPageResponseFormatter;
use App\Formatter\Site\ShopListResponseFormatter;
use App\Repository\CareerDescriptionRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

final class JobPageController extends AbstractController
{
    /**
     * @var CareerDescriptionRepository
     */
    private $repository;

    /**
     * @var JobPageResponseFormatter
     */
    private $responseFormatter;

    /**
     * @param CareerDescriptionRepository $repository
     * @param JobPageResponseFormatter    $responseFormatter
     */
    public function __construct(
        CareerDescriptionRepository $repository,
        JobPageResponseFormatter $responseFormatter
    ) {
        $this->repository = $repository;
        $this->responseFormatter = $responseFormatter;
    }

    /**
     * @Route({
     *          "rs": "/radna-mesta",
     *          "en": "/jobs"
     *     },
     *     name="site.jobs_list_page",
     *     methods={"GET"},
     *     options={"expose": true}
     * )
     * @Template("Site/Pages/career.html.twig")
     *
     * @param Request $request
     * @param int     $page
     * @param string  $searchData
     *
     * @return array
     */
    public function index(Request $request): array
    {
        $locale = $request->getSession()->get('_locale');
        $data = $this->repository->getActiveList($locale);

        return $this->responseFormatter->formatResponse($data);
    }

    /**
     * @Route({
     *          "rs": "/radna-mesta/{slug}",
     *          "en": "/jobs/{slug}"
     *     },
     *     name="site.jobs_detail_page",
     *     methods={"GET"},
     *     options={"expose": true}
     * )
     * @Template("Site/Pages/jobPage.html.twig")
     *
     * @param CareerDescriptionTranslation $careerDescriptionTranslation
     * @param Request                      $request
     *
     * @return array
     */
    public function detailPage(CareerDescriptionTranslation $careerDescriptionTranslation, Request $request): array
    {
        $locale = $request->getSession()->get('_locale');
        $data = $this->repository->getActiveList($locale);

        return [
            'job' => $careerDescriptionTranslation->getCareerDescription(),
            'trans' => $careerDescriptionTranslation,
        ];
    }
}
