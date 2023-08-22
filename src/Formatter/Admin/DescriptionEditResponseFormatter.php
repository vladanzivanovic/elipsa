<?php

declare(strict_types=1);

namespace App\Formatter\Admin;

use App\Entity\Description;
use App\Helper\ConstantsHelper;
use App\Repository\DescriptionRepository;
use Symfony\Contracts\Translation\TranslatorInterface;

final class DescriptionEditResponseFormatter
{
    private DescriptionRepository $repository;

    private ConstantsHelper $constantsHelper;

    private TranslatorInterface $translator;

    public function __construct(
        DescriptionRepository $repository,
        ConstantsHelper $constantsHelper,
        TranslatorInterface $translator
    ) {
        $this->repository = $repository;
        $this->constantsHelper = $constantsHelper;
        $this->translator = $translator;
    }

    /**
     * @param int|null $type
     *
     * @return array
     */
    public function formatResponse(?int $type = null): array
    {
        $response = [
          'typeOptions' => $this->getTypesForOptions(),
        ];

        if (null !== $type) {

            $descriptions = $this->repository->findBy(['type' => $type]);

            foreach ($descriptions as $description) {
                $response[$description->getLocale() . '_description'] = $description->getDescription();
            }

            $response['type'] = $type;
        }

        return $response;
    }

    private function getTypesForOptions(): array
    {
        $constants = $this->constantsHelper->getClassConstants(Description::class);

        $options = [];

        foreach ($constants as $constant => $value) {
            $options[] = [
                'title' => $this->translator->trans('navi.'.$value),
                'value' => $constant,
            ];
        }

        return $options;
    }
}
