<?php

namespace App\Entity;

use App\Entity\Resources\EntityInterface;
use App\Entity\Resources\ResourceTrait;
use App\Repository\PromotionOptionRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: PromotionOptionRepository::class)]
class PromotionOption implements EntityInterface
{
    use ResourceTrait;

    const OPTION_CATEGORIES = 'categories';

    const OPTION_TAGS = 'tags';

    const OPTION_PRODUCTS = 'products';

    const OPTION_ALL_PRODUCTS = 'applicable_all_products';

    #[ORM\ManyToOne(targetEntity: Promotion::class, inversedBy: 'promotionOptions')]
    #[ORM\JoinColumn(nullable: false)]
    private Promotion $promotionId;

    #[ORM\Column(type: 'string', length: 255)]
    private string $type;

    #[ORM\Column(type: 'json')]
    private array $configuration = [];

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
