<?php

declare(strict_types=1);

namespace App\Controller\Admin;

use App\Collector\Admin\TagEditCollector;
use App\Entity\TagTranslation;
use App\Formatter\Admin\TagEditFormatter;
use Symfony\Bridge\Twig\Attribute\Template;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

final class TagEditPageController extends AbstractController
{
    public function __construct(
        private readonly TagEditCollector $tagEditCollector,
        private readonly TagEditFormatter $tagEditFormatter,
    ) {}

    #[Route(path: '/add-product-tag', name: 'admin.add_product_tag_page', methods: ['GET'])]
    #[Route(path: '/add-blog-tag', name: 'admin.add_blog_tag_page', methods: ['GET'])]
    #[Template('Admin/Pages/tagEdit.html.twig')]
    public function insert(): array
    {
        $collectedData = $this->tagEditCollector->collect();

        return $this->tagEditFormatter->format($collectedData);
    }

    
    #[Route(path: '/edit-product-tag/{slug}', name: 'admin.edit_product_tag_page', methods: ['GET'])]
    #[Route(path: '/edit-blog-tag/{slug}', name: 'admin.edit_blog_tag_page', methods: ['GET'])]
    #[Template('Admin/Pages/tagEdit.html.twig')]
    public function update(TagTranslation $tagTranslation): array
    {
        $collectedData = $this->tagEditCollector->collect($tagTranslation->getTag());

        return $this->tagEditFormatter->format($collectedData);
    }
}
