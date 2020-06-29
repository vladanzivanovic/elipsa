<?php

declare(strict_types=1);

namespace App\Controller\Admin\Api;

use App\Parser\DescriptionRequestParser;
use App\Repository\DescriptionRepository;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class DescriptionEditController extends AbstractController
{
    /**
     * @var DescriptionRequestParser
     */
    private $requestParser;

    /**
     * @var DescriptionRepository
     */
    private $repository;

    /**
     * @param DescriptionRequestParser $requestParser
     * @param DescriptionRepository    $repository
     */
    public function __construct(
        DescriptionRequestParser $requestParser,
        DescriptionRepository $repository
    ) {
        $this->requestParser = $requestParser;
        $this->repository = $repository;
    }

    /**
     * @Route("/api/set-description", name="admin.set_description_api", methods={"POST", "PUT"}, options={"expose": true})
     *
     * @param Request $request
     *
     * @return JsonResponse
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function set(Request $request): JsonResponse
    {
        $this->requestParser->parse($request->request);

        $this->repository->flush();

        return $this->json(null, JsonResponse::HTTP_CREATED);
    }

    /**
     * @Route("/api/description-remove/{type}", name="admin.remove_description_api", methods={"DELETE"}, options={"expose": true})
     *
     * @param int $type
     *
     * @return JsonResponse
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function remove(int $type): JsonResponse
    {
        $descriptions = $this->repository->findBy(['type' => $type]);

        foreach ($descriptions as $description) {
            $this->repository->delete($description);
        }

        $this->repository->flush();

        return $this->json(null);
    }
}