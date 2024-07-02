<?php

namespace App\Entity;

use App\Entity\Resources\EntityInterface;
use App\Entity\Resources\ResourceTrait;
use App\Repository\ProductSizeRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;

#[ORM\Entity(repositoryClass: ProductSizeRepository::class)]
class ProductSize implements EntityInterface
{
    use ResourceTrait;

    public const NO_SIZE = 'no-sizes';

    #[ORM\Column(type: 'string')]
    private string $size;

    /**
     * @var Collection<int, ProductHasSizes>
     */
    #[ORM\OneToMany(mappedBy: 'size', targetEntity: ProductHasSizes::class)]
    private Collection $productHasSizes;

    #[ORM\Column(type: 'string', length: 255)]
    #[Gedmo\Slug(fields: ['size'], updatable: true)]
    private string $slug;

    public function __construct()
    {
        $this->productHasSizes = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getSize(): ?string
    {
        return $this->size;
    }

    public function setSize(string $size): self
    {
        $this->size = $size;

        return $this;
    }

    /**
     * @return Collection<int, ProductHasSizes>
     */
    public function getProductHasSizes(): Collection
    {
        return $this->productHasSizes;
    }

    public function addProductHasSize(ProductHasSizes $productHasSize): self
    {
        if (!$this->productHasSizes->contains($productHasSize)) {
            $this->productHasSizes[] = $productHasSize;
            $productHasSize->setSize($this);
        }

        return $this;
    }

    public function removeProductHasSize(ProductHasSizes $productHasSize): self
    {
        if ($this->productHasSizes->contains($productHasSize)) {
            $this->productHasSizes->removeElement($productHasSize);
            // set the owning side to null (unless already changed)
            if ($productHasSize->getSize() === $this) {
                $productHasSize->setSize(null);
            }
        }

        return $this;
    }

    public function getSlug(): ?string
    {
        return $this->slug;
    }

    public function setSlug(string $slug): self
    {
        $this->slug = $slug;

        return $this;
    }
}
