<?php

declare(strict_types=1);

namespace App\Twig;

use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class RequestExtension extends AbstractExtension
{
    private \Symfony\Component\HttpFoundation\RequestStack $request;

    public function __construct(
        RequestStack $request
    ) {
        $this->request = $request;
    }

    /**
     * @return array
     */
    public function getFunctions()
    {
        return [
            new TwigFunction('routeName', [$this, 'getRouteName']),
        ];
    }

    
    public function getRouteName(): string
    {
        return $this->request->getMainRequest()->attributes->get('_route');
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'request_extension';
    }
}
