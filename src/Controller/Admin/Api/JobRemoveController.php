<?php

declare(strict_types=1);

namespace App\Controller\Admin\Api;

use App\Entity\Blog;
use App\Entity\CareerDescription;
use App\Handler\BlogHandler;
use App\Handler\JobHandler;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;

final class JobRemoveController extends AbstractController
{
    private \Symfony\Contracts\Translation\TranslatorInterface $translator;

    public function __construct(
        JobHandler $handler,
        TranslatorInterface $translator
    ) {
        $this->translator = $translator;
    }

    /**
     *
     *
     * @throws ORMException
     * @throws OptimisticLockException
     */
    #[Route(path: '/api/job-remove/{id}', name: 'admin.remove_job_api', methods: ['DELETE'], options: ['expose' => true])]
    public function remove(CareerDescription $careerDescription): JsonResponse
    {
        $careerDescription->setStatus(CareerDescription::STATUS_ARCHIVED);

        if ($careerDescription->getCareers()->count() > 0) {
            return $this->json(['message' => $this->translator->trans('error.in_use', ['%item%' => 'Radno mesto'])], JsonResponse::HTTP_BAD_REQUEST);
        }

        return $this->json(null);
    }
}