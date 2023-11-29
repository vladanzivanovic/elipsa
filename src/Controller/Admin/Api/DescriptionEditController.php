<?php

declare(strict_types=1);

namespace App\Controller\Admin\Api;

use App\Parser\DescriptionRequestParser;
use App\Repository\DescriptionRepository;
use App\Request\Dto\DescriptionRequestDto;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class DescriptionEditController extends AbstractController
{
    private DescriptionRequestParser $requestParser;

    private DescriptionRepository $repository;

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
    public function set(DescriptionRequestDto $descriptionRequestDto): JsonResponse
    {
        $this->requestParser->parse($descriptionRequestDto);

        $this->repository->flush();

        return $this->json(null, Response::HTTP_CREATED);
    }

    /**
     * @Route("/api/description-remove/{type}", name="admin.remove_description_api", methods={"DELETE"}, options={"expose": true})
     *
     * @param string $type
     *
     * @return JsonResponse
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function remove(string $type): JsonResponse
    {
        $descriptions = $this->repository->findBy(['type' => $type]);

        foreach ($descriptions as $description) {
            $this->repository->delete($description);
        }

        $this->repository->flush();

        return $this->json(null);
    }
}
