<?php

declare(strict_types=1);

namespace App\Formatter\Admin;

use App\Entity\Banner;
use App\Entity\Blog;
use App\Repository\ImageRepository;
use App\Repository\TagsRepository;
use Symfony\Component\Routing\RouterInterface;

final class BlogEditResponseFormatter
{
    use ImageTrait;

    /**
     * @var RouterInterface
     */
    private $router;

    /**
     * @var TagsRepository
     */
    private $tagsRepository;

    /**
     * @param RouterInterface $router
     * @param TagsRepository  $tagsRepository
     */
    public function __construct(
        RouterInterface $router,
        TagsRepository $tagsRepository
    ) {
        $this->router = $router;
        $this->tagsRepository = $tagsRepository;
    }

    /**
     * @param Blog $blog
     *
     * @return array
     */
    public function formatResponse(Blog $blog): array
    {
        $rsTrans = $blog->getBlogTranslationByLocale('rs');
        $enTrans = $blog->getBlogTranslationByLocale('en');

        $image = $blog->getImage();

        $imageArray = [
            'id' => $image->getId(),
            'fileName' => $image->getName(),
            'isMain' => $image->getIsMain(),
        ];

        return [
            'rs_description' => $rsTrans->getDescription(),
            'rs_short_description' => $rsTrans->getShortDescription(),
            'rs_title' => $rsTrans->getTitle(),
            'en_description' => $enTrans->getDescription(),
            'en_short_description' => $enTrans->getShortDescription(),
            'en_title' => $enTrans->getTitle(),
            'selectedTags' => array_column($this->tagsRepository->getByBlog($blog), 'mainSlug'),
            'selectedImages' => $this->imagesFormatter($this->router, [$imageArray], 'blog'),
        ];
    }

    /**
     * @param Banner $banner
     *
     * @return array
     */
    private function getImage(Banner $banner): array
    {
        $image = $banner->getImage();

        return [
            'id' => $image->getId(),
            'fileName' => $image->getName(),
            'isMain' => $image->getIsMain(),
        ];
    }
}