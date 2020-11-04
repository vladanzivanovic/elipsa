<?php

declare(strict_types=1);

namespace App\Controller\Site;

use App\Entity\Catalogue;
use App\Entity\CatalogueTranslation;
use App\Entity\Image;
use App\Formatter\Admin\ImageTrait;
use App\Repository\CatalogueRepository;
use App\Repository\ImageRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\RouterInterface;

final class CatalogDetailPageController extends AbstractController
{
    use ImageTrait;

    /**
     * @var ImageRepository
     */
    private $imageRepository;
    /**
     * @var RouterInterface
     */
    private $router;
    /**
     * @var CatalogueRepository
     */
    private $catalogueRepository;

    /**
     * @param ImageRepository     $imageRepository
     * @param RouterInterface     $router
     * @param CatalogueRepository $catalogueRepository
     */
    public function __construct(
        ImageRepository $imageRepository,
        RouterInterface $router,
        CatalogueRepository $catalogueRepository
    ) {
        $this->imageRepository = $imageRepository;
        $this->router = $router;
        $this->catalogueRepository = $catalogueRepository;
    }

    /**
     * @Route({
     *          "rs": "/katalog/{slug}",
     *          "en": "/catalogue/{slug}"
     *     },
     *     name="site.catalog_detail_page",
     *     methods={"GET"}
     * )
     * @Template("Site/Pages/catalogDetail.html.twig")
     *
     * @param CatalogueTranslation $catalogueTranslation
     *
     * @return array
     */
    public function getCatalog(CatalogueTranslation $catalogueTranslation): array
    {
        return [
            'title' => $catalogueTranslation->getTitle(),
            'images' => $this->imagesFormatter(
            $this->router,
            $this->imageRepository->getByCatalog($catalogueTranslation->getCatalogue()),
            'catalog',
            'list_thumb'
        )];
    }
}