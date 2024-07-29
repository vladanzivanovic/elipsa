<?php

declare(strict_types=1);

namespace App\Controller\Admin\Api;

use App\Entity\CareerDescription;
use App\Entity\Resources\StatusInterface;
use App\Handler\JobHandler;
use App\Parser\JobRequestParser;
use App\Request\Dto\Admin\CareerDescriptionEditRequestDto;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use ReflectionException;
use App\Helper\ConstantsHelper;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class JobEditController extends AbstractController
{
    private \App\Parser\JobRequestParser $requestParser;

    private \App\Handler\JobHandler $handler;

    public function __construct(
        JobRequestParser $requestParser,
        JobHandler $handler
    ) {
        $this->requestParser = $requestParser;
        $this->handler = $handler;
    }

    /**
     * @throws ORMException
     * @throws OptimisticLockException
     */
    #[Route(path: '/api/job-create', name: 'admin.add_job_api', options: ['expose' => true], methods: ['POST'])]
    public function insert(CareerDescriptionEditRequestDto $careerDescriptionEditRequestDto): JsonResponse
    {
        $blog = $this->requestParser->parse($careerDescriptionEditRequestDto);

        $this->handler->save($blog);

        return $this->json(null, Response::HTTP_CREATED);
    }


    /** todo investigate this */
    #[Route(path: '/api/job-edit/{id}', name: 'admin.edit_job_api', options: ['expose' => true], methods: ['PUT'])]
    public function edit(CareerDescription $careerDescription, CareerDescriptionEditRequestDto $careerDescriptionEditRequestDto): JsonResponse
    {
        $this->requestParser->parse($careerDescriptionEditRequestDto, $careerDescription);

        $this->handler->save($careerDescription);

        return $this->json(null, Response::HTTP_CREATED);
    }

    /**
     *
     * @throws ReflectionException
     */
    #[Route(path: '/api/job-set-status/{status}/{id}', name: 'admin.set_job_status_api', options: ['expose' => true], methods: ['PATCH'])]
    public function changeStatus(CareerDescription $careerDescription, string $status): JsonResponse
    {
        $careerDescription->setStatus($status);
        $careerDescription->setActivationDate(new \DateTime());

        $this->handler->save($careerDescription);

        $statusText = ConstantsHelper::getConstantName((string) $status, 'STATUS', StatusInterface::class);

        return $this->json(['text' => $statusText], JsonResponse::HTTP_CREATED);
    }
}
