<?php

declare(strict_types=1);

namespace App\Request\Dto;

use App\Provider\GoogleApiProvider;
use Symfony\Component\HttpFoundation\Request;

final class GoogleApiRequestDto extends AbstractRequestDto
{
    public string $query;

    public string $type;

    public function __construct(Request $request)
    {
        $query = $request->query;

        $this->query = $query->get('query');
        $this->type = $query->get('type') ?? GoogleApiProvider::TYPE_ADDRESS;

        parent::__construct($request);
    }
}
