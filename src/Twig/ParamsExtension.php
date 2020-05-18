<?php

declare(strict_types=1);

namespace App\Twig;

use Symfony\Component\DependencyInjection\ContainerInterface;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class ParamsExtension extends AbstractExtension
{
    private $container;

    /**
     * ParamsExtension constructor.
     *
     * @param ContainerInterface    $container
     */
    public function __construct(
        ContainerInterface $container
    ) {
        $this->container = $container;
    }

    /**
     * @return array
     */
    public function getFunctions()
    {
        return [
            new TwigFunction('app_params', [$this, 'getParams']),
            new TwigFunction('param_exist', [$this, 'isSetAndExist']),
            new TwigFunction('value_from_param', [$this, 'getValueFromParam']),
        ];
    }

    /**
     * @param $parameter
     *
     * @return mixed
     */
    public function getParams($parameter)
    {
        return $this->container->getParameter($parameter);
    }

    /**
     * @param $value
     *
     * @return bool
     */
    public function isSetAndExist($value)
    {
        return !empty($value);
    }

    /**
     * @param             $value
     * @param string      $param
     * @param string|null $arrayKey
     *
     * @return mixed
     */
    public function getValueFromParam($value, string $param, string $arrayKey = null)
    {
        $params = $this->getParams($param);

        if (is_string($arrayKey)) {
            $params = $params[$arrayKey];
        }

        return $params[$value];
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'params_extension';
    }
}