<?php

declare(strict_types=1);

namespace App\Formatter\Admin;

use App\Entity\Blog;
use App\Helper\ConstantsHelper;
use App\Model\DataTableModel;

final class BlogDataTableResponseFormatter
{
    use DataTableResponseTrait;

    /**
     * @param DataTableModel $tableModel
     * @param array          $blogs
     * @param int            $total
     *
     * @return array
     */
    public function formatResponse(DataTableModel $tableModel, array $blogs, int $total): array
    {
        $blogs = array_map(function ($blog) {
            $blog['status_text'] = ConstantsHelper::getConstantName((string)$blog['status'], 'STATUS', Blog::class);

            return $blog;
        }, $blogs);

        return $this->response($tableModel, $blogs, $total);

    }
}