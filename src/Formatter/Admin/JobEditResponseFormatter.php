<?php

declare(strict_types=1);

namespace App\Formatter\Admin;

use App\Entity\Banner;
use App\Entity\Blog;
use App\Entity\CareerDescription;
use App\Repository\ImageRepository;
use App\Repository\TagsRepository;
use Symfony\Component\Routing\RouterInterface;

final class JobEditResponseFormatter
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
     * @param CareerDescription $careerDescription
     *
     * @return array
     */
    public function formatResponse(CareerDescription $careerDescription): array
    {
        $rsTrans = $careerDescription->getTranslationByLocale('rs');
        $enTrans = $careerDescription->getTranslationByLocale('en');

        $image = $careerDescription->getImage();

        $imageArray = [
            'id' => $image->getId(),
            'fileName' => $image->getName(),
            'isMain' => $image->getIsMain(),
        ];

        return [
            'rs_description' => $rsTrans->getDescription(),
            'rs_title' => $rsTrans->getTitle(),
            'en_description' => $enTrans->getDescription(),
            'en_title' => $enTrans->getTitle(),
            'selectedImages' => $this->imagesFormatter($this->router, [$imageArray], 'blog'),
        ];
    }
}