<?php

declare(strict_types=1);

namespace App\Request\Dto;

use Nelexa\RequestDtoBundle\Dto\ConstructRequestObjectInterface;
use Symfony\Component\HttpFoundation\Request;

final class OrderStateRequestDto extends OrderRequestDto
{
    public string $status;

    public ?array $trackingInfo = null;

    public function __construct(Request $request)
    {
        $body = json_decode($request->getContent(), true);

        $this->status = $body['status'];

        $this->trackingInfo = 0 < count($body['tracking_info']) ? $body['tracking_info'] : null;

        parent::__construct($request);
    }
}
