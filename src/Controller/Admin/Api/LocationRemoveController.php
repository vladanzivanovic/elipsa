<?php

declare(strict_types=1);

namespace App\Controller\Admin\Api;

use App\Entity\Location;
use App\Entity\Resources\StatusInterface;
use App\Handler\LocationHandler;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;

final class LocationRemoveController extends AbstractController
{
    public function __construct(
        private readonly LocationHandler $locationHandler,
    ) {}

    /**
     *
     *
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    #[Route(path: '/api/remove-location/{id}', name: 'admin.remove_location_api', methods: ['DELETE'], options: ['expose' => true])]
    public function remove(Location $location): JsonResponse
    {
        if ($location->hasOrders()) {
            $location->setStatus(StatusInterface::STATUS_ARCHIVED);

            $this->locationHandler->save($location);

            return $this->json(null);
        }

        $this->locationHandler->remove($location);

        return $this->json(null);
    }
}
