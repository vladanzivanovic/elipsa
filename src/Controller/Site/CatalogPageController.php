<?php

declare(strict_types=1);

namespace App\Controller\Site;

use App\Entity\Image;
use App\Repository\ImageRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\RouterInterface;

final class CatalogPageController extends AbstractController
{
    /**
     * @var ImageRepository
     */
    private $imageRepository;
    /**
     * @var RouterInterface
     */
    private $router;

    /**
     * CatalogPageController constructor.
     *
     * @param ImageRepository $imageRepository
     * @param RouterInterface $router
     */
    public function __construct(
        ImageRepository $imageRepository,
        RouterInterface $router
    ) {
        $this->imageRepository = $imageRepository;
        $this->router = $router;
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
     * @return array
     */
    public function getImages(): array
    {
        $images = $this->imageRepository->getByType(Image::RELATED_TYPE_CATALOG);

        return ['images' => array_map(function ($image) {
            $image['link'] = $this->router->generate('app.image_show', ['entity' => 'catalog', 'name' => $image['fileName'], 'filter' => 'list_thumb']);

            return $image;
        }, $images)];
    }
}