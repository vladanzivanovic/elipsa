<?php

declare(strict_types=1);

namespace App\Formatter\Admin;

use App\Entity\Banner;
use App\Entity\Blog;
use App\Entity\CareerDescription;
use App\Repository\ImageRepository;
use App\Repository\TagsRepository;
use App\View\CareerDescriptionView;
use Symfony\Component\Routing\RouterInterface;

final class JobEditResponseFormatter
{
    use ImageTrait;

    public function __construct(
        private readonly RouterInterface $router,
        private readonly TagsRepository $tagsRepository,
        private readonly CareerDescriptionView $careerDescriptionView
    ) {}

    
    public function formatResponse(CareerDescription $careerDescription): array
    {
        return [
            'payload' => $this->careerDescriptionView->editView($careerDescription),
        ];
    }
}
