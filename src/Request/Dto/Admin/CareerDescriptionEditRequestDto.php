<?php

declare(strict_types=1);

namespace App\Request\Dto\Admin;

use Symfony\Component\HttpFoundation\Request;

final class CareerDescriptionEditRequestDto extends AbstractEditRequestDto
{
    public function __construct(Request $request)
    {
        parent::__construct($request);
    }
}
