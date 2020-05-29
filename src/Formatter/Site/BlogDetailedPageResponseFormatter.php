<?php

declare(strict_types=1);

namespace App\Formatter\Site;

use App\Entity\Image;
use Symfony\Component\Routing\RouterInterface;

final class BlogDetailedPageResponseFormatter
{
    use FormatterTrait;

    /**
     * @var RouterInterface
     */
    private $router;

    /**
     * @param RouterInterface $router
     */
    public function __construct(
        RouterInterface $router
    ) {
        $this->router = $router;
    }

    public function formatResponse(array $data): array
    {
        /** @var Image $image */
        $image = $data['blog']->getImage();

        $data['image_link'] = $this->router->generate('app.image_show', ['entity' => 'blog', 'name' => $image->getName(), 'filter' => 'blog_list']);

        return $data;
    }
}