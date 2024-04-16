<?php

namespace App\Entity;

use App\Repository\OfficeContactRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=OfficeContactRepository::class)
 */
class OfficeContact implements EntityInterface
{
    use ResourceTrait;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private string $telephone;

    /**
     * @ORM\OneToMany(targetEntity=OfficeContactTranslation::class, mappedBy="officeContact", orphanRemoval=true, cascade={"persist", "remove"})
     *
     * @var Collection<int, OfficeContactTranslation>
     */
    private Collection $officeContactTranslations;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    private bool $showInFooter;

    /**
     * @ORM\Column(type="boolean")
     */
    private bool $useInEmail = false;

    public function __construct()
    {
        $this->officeContactTranslations = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTelephone(): string
    {
        return $this->telephone;
    }

    public function setTelephone(string $telephone): void
    {
        $this->telephone = $telephone;
    }

    /**
     * @return Collection<int, OfficeContactTranslation>
     */
    public function getOfficeContactTranslations(): Collection
    {
        return $this->officeContactTranslations;
    }

    public function addOfficeContactTranslation(OfficeContactTranslation $officeContactTranslation): self
    {
        if (!$this->officeContactTranslations->contains($officeContactTranslation)) {
            $this->officeContactTranslations[] = $officeContactTranslation;
            $officeContactTranslation->setOfficeContact($this);
        }

        return $this;
    }

    public function removeOfficeContactTranslation(OfficeContactTranslation $officeContactTranslation): self
    {
        if ($this->officeContactTranslations->removeElement($officeContactTranslation)) {
            // set the owning side to null (unless already changed)
            if ($officeContactTranslation->getOfficeContact() === $this) {
                $officeContactTranslation->setOfficeContact(null);
            }
        }

        return $this;
    }

    public function getByLocale(string $locale): OfficeContactTranslation|null
    {
        $transCollection = $this->officeContactTranslations;

        $filtered = $transCollection->filter(function ($trans) use ($locale) {
            /** @var OfficeContactTranslation $trans */
            return $trans->getLocale() === $locale;
        });

        return 0 < $filtered->count() ? $filtered->first() : null;
    }

    public function isShownInFooter(): bool
    {
        return $this->showInFooter;
    }

    public function setShowInFooter(bool $showInFooter): void
    {
        $this->showInFooter = $showInFooter;
    }

    public function isUseInEmail(): ?bool
    {
        return $this->useInEmail;
    }

    public function setUseInEmail(bool $useInEmail): self
    {
        $this->useInEmail = $useInEmail;

        return $this;
    }
}
