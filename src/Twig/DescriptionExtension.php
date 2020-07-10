<?php

declare(strict_types=1);

namespace App\Twig;

use App\Repository\DescriptionRepository;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

final class DescriptionExtension extends AbstractExtension
{
    /**
     * @var DescriptionRepository
     */
    private $repository;
    /**
     * @var SessionInterface
     */
    private $session;

    /**
     * @param DescriptionRepository $repository
     * @param SessionInterface      $session
     */
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
    public function getFunctions()
    {
        return [
            new TwigFunction('description', [$this, 'getDescription']),
        ];
    }

    /**
     * @param int    $type
     * @param string $filter
     * @param string $locale
     * @param bool   $isMobile
     *
     * @return string|null
     */
    public function getDescription(int $type): ?string
    {
        $description = $this->repository->findOneBy(['type' => $type, 'locale' => $this->session->get('_locale')]);

        return null !== $description ? $description->getDescription() : null;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'banner_extension';
    }
}
