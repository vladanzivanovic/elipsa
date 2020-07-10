<?php

declare(strict_types=1);

namespace App\Controller\Admin;

use App\Entity\ProductColor;
use App\Entity\Tags;
use App\Repository\ProductColorRepository;
use App\Repository\TagsRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

final class TagEditPageController extends AbstractController
{
    /**
     * @var TagsRepository
     */
    private $tagsRepository;
    /**
     * @var ParameterBagInterface
     */
    private $bag;

    /**
     * TagEditPageController constructor.
     *
     * @param TagsRepository        $tagsRepository
     * @param ParameterBagInterface $bag
     */
    public function __construct(
        TagsRepository $tagsRepository,
        ParameterBagInterface $bag
    ) {
        $this->tagsRepository = $tagsRepository;
        $this->bag = $bag;
    }

    /**
     * @Route("/add-product-tag", name="admin.add_product_tag_page", methods={"GET"})
     * @Route("/add-blog-tag", name="admin.add_blog_tag_page", methods={"GET"})
     * @Template("Admin/Pages/tagEdit.html.twig")
     *
     * @return array
     */
    public function insert(): array
    {
        return [];
    }

    /**
     * @Route("/edit-product-tag/{slug}", name="admin.edit_product_tag_page", methods={"GET"})
     * @Route("/edit-blog-tag/{slug}", name="admin.edit_blog_tag_page", methods={"GET"})
     * @Template("Admin/Pages/tagEdit.html.twig")
     *
     * @param Tags    $tag
     * @param Request $request
     *
     * @return array
     */
    public function update(Tags $tag, Request $request): array
    {
        $relatedType = $request->attributes->get('_route') === 'admin.edit_blog_tag_page' ? Tags::TYPE_BLOG : Tags::TYPE_PRODUCT;

        $locales = explode('|', $this->bag->get('locales'));
        $tagsByLocale = $this->tagsRepository->getByMainSlugAndLocales($tag->getMainSlug(), $locales, $relatedType);

        $responseArray = [];

        foreach ($tagsByLocale as $localeItem) {
            $responseArray[$localeItem['locale'].'_title'] = $localeItem['label'];
        }

        return $responseArray;
    }
}