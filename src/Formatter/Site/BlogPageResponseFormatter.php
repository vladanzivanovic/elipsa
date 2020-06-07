<?php

declare(strict_types=1);

namespace App\Formatter\Site;

use App\Repository\TagsRepository;
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
     * @var TagsRepository
     */
    private $tagsRepository;

    /**
     * @param RouterInterface       $router
     * @param ParameterBagInterface $bag
     * @param TagsRepository        $tagsRepository
     */
    public function __construct(
        RouterInterface $router,
        ParameterBagInterface $bag,
        TagsRepository $tagsRepository
    ) {
        $this->router = $router;
        $this->bag = $bag;
        $this->tagsRepository = $tagsRepository;
    }

    /**
     * @param array<string, array<array<string|int, mixed>>> $data
     *
     * @param string                                         $locale
     *
     * @return array<string, array<array<string|int, mixed>>>
     */
    public function formatResponse(array $data, string $locale): array
    {
        $data['blog_list']['data'] = array_map(function ($blog) {
            $blog['image_link_list'] = $this->router->generate('app.image_show', ['entity' => 'blog', 'name' => $blog['imageName'], 'filter' => 'blog_list']);

            return $blog;
        }, $data['blog_list']['data']);

        $data['pagination'] = $data['blog_list']['pagination'];
        $data['blog_list'] = $data['blog_list']['data'];

        if (null !== $data['localized_url']) {
            $data['localized_url'] = $this->router->generate('site.blog_list_page', ['_locale' => $locale === 'rs' ? 'en' : 'rs', 'tag' => $data['localized_url']]);
        }

        return $data;
    }
}