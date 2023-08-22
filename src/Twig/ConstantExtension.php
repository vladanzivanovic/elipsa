<?php

declare(strict_types=1);

namespace App\Twig;

use App\Helper\ConstantsHelper;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class ConstantExtension extends AbstractExtension
{
    private ConstantsHelper $constantsHelper;

    public function __construct(ConstantsHelper $constantsHelper)
    {

        $this->constantsHelper = $constantsHelper;
    }

    /**
     * @return array
     */
    public function getFunctions(): array
    {
        return [
            new TwigFunction('get_class_constants', [$this, 'getClassConstants']),
        ];
    }

    /**
     * @return array<string, mixed>
     *
     * @throws \ReflectionException
     */
    public function getClassConstants(string $className): array
    {
        return $this->constantsHelper->getClassConstants($className);
    }

    public function getName(): string
    {
        return 'constant_extension';
    }
}
