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
//        $rsTrans = $blog->getBlogTranslationByLocale('rs');
//        $enTrans = $blog->getBlogTranslationByLocale('en');
//
//        $image = $blog->getImage();
//
//        $imageArray = [
//            'id' => $image->getId(),
//            'fileName' => $image->getName(),
//            'isMain' => $image->getIsMain(),
//        ];

        $payload = null;

        if (null !== $blog) {
            $payload = $this->blogView->editView($blog);
        }

        $formattedOptions = [
            'tags' => $this->tagOptionsFormatter->formatTagOptions(Tags::TYPE_BLOG),
        ];

        return [
            'payload' => $payload,
            'options' => $formattedOptions,
        ];

//        return [
//            'rs_description' => $rsTrans->getDescription(),
//            'rs_short_description' => $rsTrans->getShortDescription(),
//            'rs_title' => $rsTrans->getTitle(),
//            'en_description' => $enTrans->getDescription(),
//            'en_short_description' => $enTrans->getShortDescription(),
//            'en_title' => $enTrans->getTitle(),
//            'selectedTags' => array_column($this->tagsRepository->getByBlog($blog), 'mainSlug'),
//            'selectedImages' => $this->imagesFormatter($this->router, [$imageArray], 'blog'),
//        ];
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
