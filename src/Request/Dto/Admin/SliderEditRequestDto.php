<?php

declare(strict_types=1);

namespace App\Request\Dto\Admin;

use Symfony\Component\HttpFoundation\Request;

final class SliderEditRequestDto extends AbstractEditRequestDto
{
    public function __construct(Request $request)
    {
        parent::__construct($request);
    }
}
