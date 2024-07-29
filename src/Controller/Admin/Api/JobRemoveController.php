<?php

declare(strict_types=1);

namespace App\Controller\Admin\Api;

use App\Entity\Blog;
use App\Entity\CareerDescription;
use App\Entity\Resources\StatusInterface;
use App\Handler\BlogHandler;
use App\Handler\JobHandler;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Contracts\Translation\TranslatorInterface;

final class JobRemoveController extends AbstractController
{
    public function __construct(
        private readonly JobHandler $handler,
        private readonly TranslatorInterface $translator
    ) {}

    /**
     *
     *
     * @throws ORMException
     * @throws OptimisticLockException
     */
    #[Route(path: '/api/job-remove/{id}', name: 'admin.remove_job_api', options: ['expose' => true], methods: ['DELETE'])]
    public function remove(CareerDescription $careerDescription): JsonResponse
    {
        if ($careerDescription->getCareers()->count() > 0) {
            $careerDescription->setStatus(StatusInterface::STATUS_ARCHIVED);

            $this->handler->save($careerDescription);

            return $this->json(['message' => $this->translator->trans('error.in_use', ['%item%' => 'Radno mesto'])], Response::HTTP_BAD_REQUEST);
        }

        $this->handler->remove($careerDescription);

        return $this->json(null);
    }
}
