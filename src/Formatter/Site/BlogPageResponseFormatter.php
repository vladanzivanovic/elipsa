<?php

declare(strict_types=1);

namespace App\Formatter\Site;

use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\Routing\RouterInterface;

final class BlogPageResponseFormatter
{
    use FormatterTrait;

    /**
     * @var RouterInterface
     */
    private $router;
    /**
     * @var ParameterBagInterface
     */
    private $bag;

    /**
     * @param RouterInterface       $router
     * @param ParameterBagInterface $bag
     */
    public function __construct(
        RouterInterface $router,
        ParameterBagInterface $bag
    ) {
        $this->router = $router;
        $this->bag = $bag;
    }

    /**
     * @param array<string, array<array<string|int, mixed>>> $data
     *
     * @return array<string, array<array<string|int, mixed>>>
     */
    public function formatResponse(array $data): array
    {
        $data['blog_list']['data'] = array_map(function ($blog) {
            $blog['image_link_list'] = $this->router->generate('app.image_show', ['entity' => 'blog', 'name' => $blog['imageName'], 'filter' => 'blog_list']);

            return $blog;
        }, $data['blog_list']['data']);

        $data['pagination'] = $data['blog_list']['pagination'];
        $data['blog_list'] = $data['blog_list']['data'];

        return $data;
    }
}