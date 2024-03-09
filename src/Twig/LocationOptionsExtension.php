<?php

declare(strict_types=1);

namespace App\Twig;

use App\Formatter\Options\LocationOptionsFormatter;
use App\Repository\DescriptionRepository;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

final class LocationOptionsExtension extends AbstractExtension
{
    private DescriptionRepository $repository;

    private SessionInterface $session;

    private LocationOptionsFormatter $locationOptionsFormatter;

    public function __construct(
        DescriptionRepository $repository,
        SessionInterface $session,
        LocationOptionsFormatter $locationOptionsFormatter
    ) {
        $this->repository = $repository;
        $this->session = $session;
        $this->locationOptionsFormatter = $locationOptionsFormatter;
    }

    /**
     * @return array
     */
    public function getFunctions(): array
    {
        return [
            new TwigFunction('location_options', [$this, 'getOptions']),
        ];
    }

    public function getOptions(string $locale): array
    {
        return $this->locationOptionsFormatter->format($locale);
    }

    public function getName(): string
    {
        return 'location_options_extension';
    }
}
