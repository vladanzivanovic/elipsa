<?php

declare(strict_types=1);

namespace App\Formatter\Site;

use App\Entity\Catalogue;
use App\View\CatalogView;

final class CatalogueResponseFormatter
{
    private CatalogView $catalogView;

    public function __construct(
        CatalogView $catalogView
    ) {
        $this->catalogView = $catalogView;
    }

    /**
     * @param array<int, Catalogue> $catalogues
     */
    public function formatResponse(array $catalogues, string $locale): array
    {
        $catalogListView = [];

        foreach ($catalogues as $catalogue) {
            $catalogListView[] = $this->catalogView->view($catalogue, $locale);
        }

        return ['catalogues' => $catalogListView];
    }
}
