<?php

declare(strict_types=1);

namespace App\Controller\Admin;

use App\Entity\ProductColor;
use App\Entity\ProductTags;
use App\Repository\ProductColorRepository;
use App\Repository\ProductTagsRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\Routing\Annotation\Route;

final class TagEditPageController extends AbstractController
{
    /**
     * @var ProductTagsRepository
     */
    private $tagsRepository;
    /**
     * @var ParameterBagInterface
     */
    private $bag;

    /**
     * TagEditPageController constructor.
     *
     * @param ProductTagsRepository $tagsRepository
     * @param ParameterBagInterface $bag
     */
    public function __construct(
        ProductTagsRepository $tagsRepository,
        ParameterBagInterface $bag
    ) {
        $this->tagsRepository = $tagsRepository;
        $this->bag = $bag;
    }

    /**
     * @Route("/add-tag", name="admin.add_tag_page", methods={"GET"})
     * @Template("Admin/Pages/tagEdit.html.twig")
     *
     * @return array
     */
    public function insert(): array
    {
        return [];
    }

    /**
     * @Route("/edit-tag/{slug}", name="admin.edit_tag_page", methods={"GET"})
     * @Template("Admin/Pages/tagEdit.html.twig")
     *
     * @param ProductTags $productTags
     *
     * @return array
     */
    public function update(ProductTags $productTags): array
    {
        $locales = explode('|', $this->bag->get('locales'));
        $tagsByLocale = $this->tagsRepository->getByMainSlugAndLocales($productTags->getMainSlug(), $locales);

        $responseArray = [];

        foreach ($tagsByLocale as $localeItem) {
            $responseArray[$localeItem['locale'].'_title'] = $localeItem['label'];
        }

        return $responseArray;
    }
}