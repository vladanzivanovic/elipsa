<?php

declare(strict_types=1);

namespace App\View;

use App\Entity\Description;

final class DescriptionView
{
    /**
     * @param Description[] $descriptions
     */
    public function view(array $descriptions): array
    {
         $view = [
             'type' => $descriptions[0]->getType(),
         ];

         $translations = [];

        foreach ($descriptions as $description) {
            $translations[$description->getLocale()] = [
                'id' => $description->getId(),
                'description' => $description->getDescription(),
            ];
         }

        $view['translations'] = $translations;

        return $view;
    }
}
