<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Timestampable\Traits\TimestampableEntity;

#[ORM\Entity(repositoryClass: \App\Repository\CareerDescriptionRepository::class)]
class CareerDescription
{
    use TimestampableEntity;

    const STATUS_PENDING = 1;
    const STATUS_ACTIVE = 2;
    const STATUS_ARCHIVED = 3;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id;

    #[ORM\OneToOne(targetEntity: \App\Entity\Image::class, cascade: ['persist', 'remove'])]
    #[ORM\JoinColumn(nullable: false)]
    private $image;

    #[ORM\Column(type: 'smallint')]
    private $status;

    #[ORM\OneToMany(targetEntity: \App\Entity\CareerDescriptionTranslation::class, mappedBy: 'careerDescription', orphanRemoval: true, cascade: ['persist', 'remove'])]
    private $careerDescriptionTranslations;

    #[ORM\OneToMany(targetEntity: \App\Entity\Career::class, mappedBy: 'position')]
    private $careers;

    #[ORM\Column(type: 'datetime')]
    private $activationDate;

    public function __construct()
    {
        $this->careerDescriptionTranslations = new ArrayCollection();
        $this->careers = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getImage(): ?Image
    {
        return $this->image;
    }

    public function setImage(Image $image): self
    {
        $this->image = $image;

        return $this;
    }

    public function getStatus(): ?int
    {
        return $this->status;
    }

    public function setStatus(int $status): self
    {
        $this->status = $status;

        return $this;
    }

    /**
     * @return Collection|CareerDescriptionTranslation[]
     */
    public function getCareerDescriptionTranslations(): Collection
    {
        return $this->careerDescriptionTranslations;
    }

    public function addCareerDescriptionTranslation(CareerDescriptionTranslation $careerDescriptionTranslation): self
    {
        if (!$this->careerDescriptionTranslations->contains($careerDescriptionTranslation)) {
            $this->careerDescriptionTranslations[] = $careerDescriptionTranslation;
            $careerDescriptionTranslation->setCareerDescription($this);
        }

        return $this;
    }

    public function removeCareerDescriptionTranslation(CareerDescriptionTranslation $careerDescriptionTranslation): self
    {
        if ($this->careerDescriptionTranslations->contains($careerDescriptionTranslation)) {
            $this->careerDescriptionTranslations->removeElement($careerDescriptionTranslation);
            // set the owning side to null (unless already changed)
            if ($careerDescriptionTranslation->getCareerDescription() === $this) {
                $careerDescriptionTranslation->setCareerDescription(null);
            }
        }

        return $this;
    }

    /**
     * @param string $locale
     *
     * @return CareerDescriptionTranslation
     */
    public function getTranslationByLocale(string $locale): CareerDescriptionTranslation
    {
        return $this->careerDescriptionTranslations->filter(function ($trans) use ($locale) {
            /** @var CareerDescriptionTranslation $trans */
            return $trans->getLocale() == $locale;
        })->first();
    }

    /**
     * @return Collection|Career[]
     */
    public function getCareers(): Collection
    {
        return $this->careers;
    }

    public function addCareer(Career $career): self
    {
        if (!$this->careers->contains($career)) {
            $this->careers[] = $career;
            $career->setPosition($this);
        }

        return $this;
    }

    public function removeCareer(Career $career): self
    {
        if ($this->careers->contains($career)) {
            $this->careers->removeElement($career);
            // set the owning side to null (unless already changed)
            if ($career->getPosition() === $this) {
                $career->setPosition(null);
            }
        }

        return $this;
    }

    public function getActivationDate(): ?\DateTimeInterface
    {
        return $this->activationDate;
    }

    public function setActivationDate(\DateTimeInterface $activationDate): self
    {
        $this->activationDate = $activationDate;

        return $this;
    }
}
