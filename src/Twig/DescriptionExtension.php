<?php

declare(strict_types=1);

namespace App\Twig;

use App\Repository\DescriptionRepository;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

final class DescriptionExtension extends AbstractExtension
{
    public function __construct(
        private readonly DescriptionRepository $repository,
    ) {}

    public function getFunctions(): array
    {
        return [
            new TwigFunction('description', [$this, 'getDescription']),
        ];
    }

    public function getDescription(string $type, string $locale): ?string
    {
        $description = $this->repository->findOneBy(['type' => $type, 'locale' => $locale]);

        return null !== $description ? $description->getDescription() : null;
    }

    public function getName(): string
    {
        return 'description_extension';
    }
}
