<?php

declare(strict_types=1);

namespace App\Controller\Admin\Api;

use App\Entity\CareerDescription;
use App\Handler\JobHandler;
use App\Parser\JobRequestParser;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use ReflectionException;
use App\Helper\ConstantsHelper;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class JobEditController extends AbstractController
{
    /**
     * @var JobRequestParser
     */
    private $requestParser;

    /**
     * @var JobHandler
     */
    private $handler;

    /**
     * @param JobRequestParser $requestParser
     * @param JobHandler       $handler
     */
    public function __construct(
        JobRequestParser $requestParser,
        JobHandler $handler
    ) {
        $this->requestParser = $requestParser;
        $this->handler = $handler;
    }

    /**
     * @Route("/api/job-create", name="admin.add_job_api", methods={"POST"}, options={"expose": true})
     *
     * @param Request $request
     *
     * @return JsonResponse
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function insert(Request $request): JsonResponse
    {
        $blog = $this->requestParser->parse($request->request);

        $this->handler->save($blog);

        return $this->json(null, JsonResponse::HTTP_CREATED);
    }

    /**
     * @Route("/api/job-edit/{id}", name="admin.edit_job_api", methods={"PUT"}, options={"expose": true})
     *
     * @param CareerDescription $careerDescription
     * @param Request           $request
     *
     * @return JsonResponse
     */
    public function edit(CareerDescription $careerDescription, Request $request): JsonResponse
    {
        $this->requestParser->parse($request->request, $careerDescription);

        $this->handler->save($careerDescription);

        return $this->json(null, JsonResponse::HTTP_CREATED);
    }

    /**
     * @Route("/api/job-set-status/{status}/{id}", name="admin.set_job_status_api", methods={"PATCH"},
     *                                                      options={"expose": true})
     *
     * @param CareerDescription $careerDescription
     * @param int               $status
     *
     * @return JsonResponse
     * @throws ReflectionException
     */
    public function changeStatus(CareerDescription $careerDescription, int $status): JsonResponse
    {
        $careerDescription->setStatus($status);
        $careerDescription->setActivationDate(new \DateTime());

        $this->handler->save($careerDescription);

        $statusText = ConstantsHelper::getConstantName((string) $status, 'STATUS', CareerDescription::class);

        return $this->json(['text' => $statusText], JsonResponse::HTTP_CREATED);
    }
}