<?php

declare(strict_types=1);

namespace App\Formatter\Site;

use App\Entity\Description;
use App\View\SiteTextView;

final class SiteTextFormatter
{
    private SiteTextView $siteTextView;

    public function __construct(
        SiteTextView $siteTextView
    ) {
        $this->siteTextView = $siteTextView;
    }

    public function format(Description $description): array
    {
        return $this->siteTextView->pageView($description);
    }
}
