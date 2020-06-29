<?php

declare(strict_types=1);

namespace App\Formatter\Admin;

use App\Repository\DescriptionRepository;

final class DescriptionEditResponseFormatter
{
    /**
     * @var DescriptionRepository
     */
    private $repository;

    /**
     * @param DescriptionRepository $repository
     */
    public function __construct(
        DescriptionRepository $repository
    ) {
        $this->repository = $repository;
    }

    /**
     * @param int $type
     *
     * @return array
     */
    public function formatResponse(int $type): array
    {
        $descriptions = $this->repository->findBy(['type' => $type]);

        $response = [];

        foreach ($descriptions as $description) {
            $response[$description->getLocale().'_description'] = $description->getDescription();
        }

        $response['type'] = $type;

        return $response;
    }
}