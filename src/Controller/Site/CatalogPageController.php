<?php

declare(strict_types=1);

namespace App\Controller\Site;

use App\Entity\Catalogue;
use App\Entity\Image;
use App\Formatter\Admin\ImageTrait;
use App\Repository\CatalogueRepository;
use App\Repository\ImageRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\RouterInterface;

final class CatalogPageController extends AbstractController
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
     *          "rs": "/katalog",
     *          "en": "/catalogue"
     *     },
     *     name="site.catalog_page",
     *     methods={"GET"}
     * )
     * @Template("Site/Pages/catalog.html.twig")
     *
     * @param Request $request
     *
     * @return array
     */
    public function getImages(Request $request): array
    {
        $catalog = $this->catalogueRepository->findOneBy(['status' => Catalogue::STATUS_ACTIVE]);
        $trans = $catalog->getByLocale($request->getLocale());

        return [
            'title' => $trans->getTitle(),
            'images' => $this->imagesFormatter(
            $this->router,
            $this->imageRepository->getByCatalog($catalog),
            'catalog',
            'list_thumb'
        )];
    }
}