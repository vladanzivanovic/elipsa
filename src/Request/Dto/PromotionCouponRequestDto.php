<?php

declare(strict_types=1);

namespace App\Request\Dto;

use Nelexa\RequestDtoBundle\Dto\ConstructRequestObjectInterface;
use Symfony\Component\HttpFoundation\Request;

class PromotionCouponRequestDto implements ConstructRequestObjectInterface
{
    public string $code;

    public \DateTimeInterface $validFrom;

    public \DateTimeInterface $validTo;

    public int $discount;

    public string $type;

    public PromotionOptionRequestDto $options;

    public function __construct(Request $request = null)
    {
        if (null === $request) {
            return;
        }

        $body = json_decode($request->getContent(), true);

        $this->type = $request->attributes->get('type');
        $this->code = $body['code'];
        $this->discount = (int) $body['discount'];
        $this->validFrom = new \DateTimeImmutable($body['valid_from'] . '00:00:00');
        $this->validTo = new \DateTimeImmutable($body['valid_to'] . '23:59:59');

        $this->options = new PromotionOptionRequestDto($body['options'] ?? null);
    }
}
