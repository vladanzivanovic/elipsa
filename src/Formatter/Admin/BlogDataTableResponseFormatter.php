<?php

declare(strict_types=1);

namespace App\Formatter\Admin;

use App\Entity\Blog;
use App\Helper\ConstantsHelper;
use App\Model\DataTableModel;

final class BlogDataTableResponseFormatter
{
    use DataTableResponseTrait;

    public function __construct(
        private readonly array $countries,
    ) {}
    
    public function formatResponse(DataTableModel $tableModel, array $blogs, int $total): array
    {
        $blogs = array_map(function ($blog) {
            $blog['status_text'] = ConstantsHelper::getConstantName((string)$blog['status'], 'STATUS', Blog::class);

            $hosts = [];

            foreach ($this->countries as $countryCode => $country) {
                foreach ($blog['available_countries'] as $availableCountryCode) {
                    if ($availableCountryCode === $countryCode) {
                        $hosts[$countryCode] = $country['host'];
                    }
                }
            }

            $blog['hosts'] = implode('<br>', $hosts);

            return $blog;
        }, $blogs);

        return $this->response($tableModel, $blogs, $total);

    }
}
