<?php

declare(strict_types=1);

namespace App\Twig;

use App\Repository\DescriptionRepository;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

final class DescriptionExtension extends AbstractExtension
{
    private DescriptionRepository $repository;

    private SessionInterface $session;

    public function __construct(
        DescriptionRepository $repository,
        SessionInterface $session
    ) {
        $this->repository = $repository;
        $this->session = $session;
    }

    /**
     * @return array
     */
    public function getFunctions(): array
    {
        return [
            new TwigFunction('description', [$this, 'getDescription']),
        ];
    }

    public function getDescription(string $type): ?string
    {
        $description = $this->repository->findOneBy(['type' => $type, 'locale' => $this->session->get('_locale')]);

        return null !== $description ? $description->getDescription() : null;
    }

    public function getName(): string
    {
        return 'description_extension';
    }
}
