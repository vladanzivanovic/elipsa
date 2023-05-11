<?php

declare(strict_types=1);

namespace App\Twig;

use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class ConstantExtension extends AbstractExtension
{
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
        $reflection = new \ReflectionClass($className);

        return $reflection->getConstants();
    }

    public function getName(): string
    {
        return 'constant_extension';
    }
}
