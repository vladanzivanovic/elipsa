<?php

declare(strict_types=1);

namespace App\Controller\Admin;

use App\Collector\Admin\TagEditCollector;
use App\Entity\ProductColor;
use App\Entity\Tags;
use App\Formatter\Admin\TagEditFormatter;
use App\Repository\ProductColorRepository;
use App\Repository\TagsRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

final class TagEditPageController extends AbstractController
{
    private TagsRepository $tagsRepository;

    private ParameterBagInterface $bag;

    private TagEditCollector $tagEditCollector;

    private TagEditFormatter $tagEditFormatter;

    public function __construct(
        TagsRepository $tagsRepository,
        ParameterBagInterface $bag,
        TagEditCollector $tagEditCollector,
        TagEditFormatter $tagEditFormatter
    ) {
        $this->tagsRepository = $tagsRepository;
        $this->bag = $bag;
        $this->tagEditCollector = $tagEditCollector;
        $this->tagEditFormatter = $tagEditFormatter;
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
        $collectedData = $this->tagEditCollector->collect();

        return $this->tagEditFormatter->format($collectedData);
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
        $collectedData = $this->tagEditCollector->collect($tag);

//        dd($this->tagEditFormatter->format($collectedData));

        return $this->tagEditFormatter->format($collectedData);
    }
}
