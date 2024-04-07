<?php

declare(strict_types=1);

namespace App\Formatter\Admin;

use App\Entity\Description;
use App\Helper\ConstantsHelper;
use App\Repository\DescriptionRepository;
use App\View\DescriptionView;
use Symfony\Contracts\Translation\TranslatorInterface;

final class DescriptionEditResponseFormatter
{
    private DescriptionRepository $repository;

    private ConstantsHelper $constantsHelper;

    private TranslatorInterface $translator;

    private DescriptionView $descriptionView;

    public function __construct(
        DescriptionRepository $repository,
        ConstantsHelper $constantsHelper,
        TranslatorInterface $translator,
        DescriptionView $descriptionView
    ) {
        $this->repository = $repository;
        $this->constantsHelper = $constantsHelper;
        $this->translator = $translator;
        $this->descriptionView = $descriptionView;
    }

    public function formatResponse(?string $type = null): array
    {
        $response = [
          'typeOptions' => $this->getTypesForOptions(),
        ];

        if (null !== $type) {

            $descriptions = $this->repository->findBy(['type' => $type]);

            $response['payload'] = $this->descriptionView->view($descriptions);

//            foreach ($descriptions as $description) {
//                $response[$description->getLocale() . '_description'] = $description->getDescription();
//            }
//
//            $response['type'] = $type;
        }

        return $response;
    }

    private function getTypesForOptions(): array
    {
        $constants = $this->constantsHelper->getClassConstants(Description::class);

        $options = [];

        foreach ($constants as $value) {
            $options[] = [
                'title' => $this->translator->trans('navi.'.$value),
                'value' => $value,
            ];
        }

        return $options;
    }
}
