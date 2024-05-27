<?php

declare(strict_types=1);

namespace App\Formatter\Admin;

use App\Entity\Banner;
use App\Entity\Blog;
use App\Entity\Tags;
use App\Formatter\Options\TagOptionsFormatter;
use App\Repository\ImageRepository;
use App\Repository\TagsRepository;
use App\View\BlogView;
use Symfony\Component\Routing\RouterInterface;

final class BlogEditResponseFormatter
{
    use ImageTrait;

    private RouterInterface $router;

    private TagsRepository $tagsRepository;

    private TagOptionsFormatter $tagOptionsFormatter;

    private BlogView $blogView;

    public function __construct(
        RouterInterface $router,
        TagsRepository $tagsRepository,
        TagOptionsFormatter $tagOptionsFormatter,
        BlogView $blogView
    ) {
        $this->router = $router;
        $this->tagsRepository = $tagsRepository;
        $this->tagOptionsFormatter = $tagOptionsFormatter;
        $this->blogView = $blogView;
    }

    public function formatResponse(Blog $blog = null): array
    {
        $payload = null;

        if ($blog instanceof Blog) {
            $payload = $this->blogView->editView($blog);
        }

        $formattedOptions = [
            'tags' => $this->tagOptionsFormatter->formatTagOptions(Tags::TYPE_BLOG),
        ];

        return [
            'payload' => $payload,
            'options' => $formattedOptions,
        ];
    }
}
