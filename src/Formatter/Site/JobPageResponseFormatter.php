<?php

declare(strict_types=1);

namespace App\Formatter\Site;

use App\Entity\Image;
use Symfony\Component\Routing\RouterInterface;

final class JobPageResponseFormatter
{
    private \Symfony\Component\Routing\RouterInterface $router;

    public function __construct(
        RouterInterface $router
    ) {
        $this->router = $router;
    }

    /**
     * @param array<string, array<array<string|int, mixed>>> $data
     *
     * @return array<string, array<array<string|int, mixed>>>
     */
    public function formatResponse(array $data): array
    {
        $data = array_map(function ($job) {
            $job['image_link_list'] = $this->router->generate('app.image_show', ['entity' => 'job', 'name' => $job['imageName'], 'filter' => 'list_thumb']);

            return $job;
        }, $data);

        return ['jobs' => $data];
    }
}