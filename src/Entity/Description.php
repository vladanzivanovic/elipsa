<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: \App\Repository\DescriptionRepository::class)]
class Description
{
    public const TYPE_LOYALTY = 'loyalty';

    public const TYPE_POLICY_AND_PRIVACY = 'policy_and_privacy';

    public const TYPE_USE_CONDITIONS = 'use_conditions';

    public const TYPE_COLLABORATOR = 'collaborator';

    public const TYPE_CAREER = 'career';

    public const TYPE_COOKIE_POLICY = 'cookie_policy';

    public const TYPE_WITHDRAW_POLICY = 'withdraw_policy';

    public const TYPE_COMPLAINT = 'complaint';

    public const TYPE_PAYMENT_METHODS = 'payment_methods';

    public const TYPE_DELIVERY = 'delivery';

    public const TYPE_FREE_DELIVERY = 'free_delivery';

    public const TYPE_IN_STORE_PICKUP = 'in_store_pickup';

    public const TYPE_PRODUCT_REHEARSAL = 'product_rehearsal';

    public const TYPE_PRODUCT_RETURN_AND_EXCHANGE = 'product_return_and_exchange';

    public const TYPE_REFUND = 'refund';

    public const TYPE_ABOUT_US = 'about_us';

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private ?int $id;

    #[ORM\Column(type: 'text')]
    private string $description;

    #[ORM\Column(type: 'string')]
    private string $type;

    #[ORM\Column(type: 'string', length: 2)]
    private string $locale;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): self
    {
        $this->description = $description;

        return $this;
    }

    public function getType(): ?string
    {
        return $this->type;
    }

    public function setType(string $type): self
    {
        $this->type = $type;

        return $this;
    }

    public function getLocale(): ?string
    {
        return $this->locale;
    }

    public function setLocale(string $locale): self
    {
        $this->locale = $locale;

        return $this;
    }
}
