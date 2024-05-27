<?php

declare(strict_types=1);

namespace App\Controller\Admin;

use App\Entity\Blog;
use App\Entity\Tags;
use App\Formatter\Admin\BlogEditResponseFormatter;
use App\Repository\TagsRepository;
use Symfony\Bridge\Twig\Attribute\Template;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

final class BlogEditPageController extends AbstractController
{
    public function __construct(
        private readonly BlogEditResponseFormatter $responseFormatter,
        private readonly TagsRepository $tagsRepository
    ) {}

    #[Route(path: '/blog/add', name: 'admin.add_blog_page', methods: ['GET'])]
    #[Template('Admin/Pages/blogEdit.html.twig')]
    public function insert(): array
    {
        return $this->responseFormatter->formatResponse();
    }

    
    #[Route(path: '/blog/{id}', name: 'admin.edit_blog_page', methods: ['GET'])]
    #[Template('Admin/Pages/blogEdit.html.twig')]
    public function update(Blog $blog): array
    {
        $blogData = $this->responseFormatter->formatResponse($blog);

        $options = [
            'tags' => $this->tagsRepository->getForOptions(Tags::TYPE_BLOG),
        ];

        return $blogData + $options;
    }
}
