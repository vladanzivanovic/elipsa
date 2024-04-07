<?php

declare(strict_types=1);

namespace App\Twig;

use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class ParamsExtension extends AbstractExtension
{
    public function __construct(
        private readonly ParameterBagInterface $parameterBag
    ) {
    }

    public function getFunctions(): array
    {
        return [
            new TwigFunction('app_params', [$this, 'getParams']),
            new TwigFunction('param_exist', [$this, 'isSetAndExist']),
            new TwigFunction('value_from_param', [$this, 'getValueFromParam']),
        ];
    }

    public function getParams($parameter): mixed
    {
        return $this->parameterBag->get($parameter);
    }

    public function isSetAndExist($value): bool
    {
        return !empty($value);
    }

    public function getValueFromParam($value, string $param, string $arrayKey = null): mixed
    {
        $params = $this->getParams($param);

        if (is_string($arrayKey)) {
            $params = $params[$arrayKey];
        }

        return $params[$value];
    }

    public function getName(): string
    {
        return 'params_extension';
    }
}
