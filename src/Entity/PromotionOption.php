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

    const RULE_CATEGORIES = 'categories';

    const RULE_TAGS = 'tags';

    const RULE_PRODUCTS = 'products';

    const RULE_COLORS = 'colors';

    const RULE_ALL_PRODUCTS = 'applicable_all_products';

    const OPTION_HOME_SCREEN_BAR = 'home_screen_bar';

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

    public function addConfiguration(mixed $value): self
    {
        $currentConfiguration = $this->getConfiguration();

        $this->configuration = array_merge_recursive($currentConfiguration, $value);

        return $this;
    }
}
