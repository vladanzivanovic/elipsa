<?php

declare(strict_types=1);

namespace App\Request\Dto\Admin;

use Nelexa\RequestDtoBundle\Dto\ConstructRequestObjectInterface;
use Symfony\Component\HttpFoundation\Request;

class PromotionCouponRequestDto extends AbstractEditRequestDto
{
    public string $code;

    public \DateTimeInterface $validFrom;

    public \DateTimeInterface $validTo;

    public int $discount;

    public string $type;

    public PromotionOptionRequestDto $options;

    /**
     * @throws \Exception
     */
    public function __construct(Request $request = null)
    {
        $body = $request->request;

        $this->type = $body->get('type');
        $this->code = $body->get('code');
        $this->discount = $body->getInt('discount');
        $this->validFrom = new \DateTimeImmutable($body->get('valid_from') . '00:00:00');
        $this->validTo = new \DateTimeImmutable($body->get('valid_to') . '23:59:59');

        $this->options = new PromotionOptionRequestDto($body->all('options') ?? null);

        parent::__construct($request);
    }
}
