<?php

namespace App\Entity;

use App\Repository\PromotionOptionRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=PromotionOptionRepository::class)
 */
class PromotionOption
{
    const OPTION_CATEGORIES = 'categories';

    const OPTION_TAGS = 'tags';

    const OPTION_PRODUCTS = 'products';

    const OPTION_ALL_PRODUCTS = 'applicable_all_products';

    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private ?int $id = null;

    /**
     * @ORM\ManyToOne(targetEntity=Promotion::class, inversedBy="promotionOptions")
     * @ORM\JoinColumn(nullable=false)
     */
    private Promotion $promotionId;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private string $type;

    /**
     * @ORM\Column(type="json")
     */
    private array $configuration = [];

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getPromotionId(): Promotion
    {
        return $this->promotionId;
    }

    public function setPromotionId(Promotion $promotionId): self
    {
        $this->promotionId = $promotionId;

        return $this;
    }

    public function getType(): string
    {
        return $this->type;
    }

    public function setType(string $type): self
    {
        $this->type = $type;

        return $this;
    }

    public function getConfiguration(): array
    {
        return $this->configuration;
    }

    public function setConfiguration(array $configuration): self
    {
        $this->configuration = $configuration;

        return $this;
    }
}
