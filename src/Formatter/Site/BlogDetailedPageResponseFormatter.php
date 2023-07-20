<?php

declare(strict_types=1);

namespace App\Formatter\Site;

use App\Entity\Image;
use App\View\TagView;
use Symfony\Component\Routing\RouterInterface;

final class BlogDetailedPageResponseFormatter
{
    use FormatterTrait;

    private RouterInterface $router;

    private TagView $tagView;

    public function __construct(
        RouterInterface $router,
        TagView $tagView
    ) {
        $this->router = $router;
        $this->tagView = $tagView;
    }

    public function formatResponse(array $data): array
    {
        /** @var Image $image */
        $image = $data['blog']->getImage();

        $data['image_link'] = $this->router->generate('app.image_show', ['entity' => 'blog', 'name' => $image->getName(), 'filter' => 'blog_list']);

        $data['tags'] = $this->formatTags($data['tags']);

        $data['blog_tags'] = $this->formatTags($data['blog_tags']);

        return $data;
    }
}
