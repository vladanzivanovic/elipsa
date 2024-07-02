<?php

declare(strict_types=1);

namespace App\Controller\Admin\Api;

use App\Repository\SettingsRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

final class SettingsEditController extends AbstractController
{
    public function __construct(
        private readonly SettingsRepository $settingsRepository
    ) {}

    #[Route(path: 'api/update-settings', name: 'admin.update_settings_api', methods: ['POST'])]
    public function update(Request $request): JsonResponse
    {
        foreach ($request->request->all() as $id => $value) {
            $setting = $this->settingsRepository->find($id);

            $setting->setValue($value);
        }

        $this->settingsRepository->flush();

        return $this->json(null);
    }
}
