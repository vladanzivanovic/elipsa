<?php

declare(strict_types=1);

namespace App\Controller\Admin\Api;

use App\Entity\Promotion;
use App\Repository\PromotionRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Contracts\Translation\TranslatorInterface;

final class PromotionCheckerController extends AbstractController
{
    private TranslatorInterface $translator;

    private string $adminLocale;

    private PromotionRepository $promotionRepository;

    public function __construct(
        TranslatorInterface $translator,
        PromotionRepository $promotionRepository,
        string $adminLocale
    ) {
        $this->translator = $translator;
        $this->adminLocale = $adminLocale;
        $this->promotionRepository = $promotionRepository;
    }
    #[Route(path: '/api/promotion/checker', name: 'admin.promotion_code_checker_api', methods: ['GET'])]
    public function checkCodeIsUnique(Request $request): JsonResponse
    {
        $code = $request->query->get('code');
        $id = $request->query->getInt('id');

        $promotion = $this->promotionRepository->findOneBy(['code' => $code]);

        return $this->json(null !== $promotion && $promotion->getId() !== $id ?
            $this->translator->trans('promotion.code_exists', [], 'validators', $this->adminLocale) :
            true
        );
    }
}
