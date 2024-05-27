<?php

namespace App\Entity;

use App\Entity\Resources\CountryResourceInterface;
use App\Entity\Resources\CountryResourceTrait;
use App\Entity\Resources\EntityInterface;
use App\Entity\Resources\LocaleTranslatorInterface;
use App\Entity\Resources\LocaleTranslatorTrait;
use App\Entity\Resources\ResourceTrait;
use App\Entity\Resources\StatusInterface;
use App\Entity\Resources\StatusTrait;
use App\Repository\CareerDescriptionRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Timestampable\Traits\TimestampableEntity;

#[ORM\Entity(repositoryClass: CareerDescriptionRepository::class)]
class CareerDescription implements EntityInterface, CountryResourceInterface, StatusInterface, LocaleTranslatorInterface
{
    use TimestampableEntity;
    use CountryResourceTrait;
    use ResourceTrait;
    use StatusTrait;
    use LocaleTranslatorTrait;

    #[ORM\OneToOne(targetEntity: Image::class, cascade: ['persist', 'remove'])]
    #[ORM\JoinColumn(nullable: false)]
    private Image $image;

    #[ORM\Column(type: 'string', length: 20)]
    private string $status;

    #[ORM\OneToMany(mappedBy: 'careerDescription', targetEntity: CareerDescriptionTranslation::class, cascade: ['persist', 'remove'], orphanRemoval: true)]
    private Collection $translations;

    #[ORM\OneToMany(mappedBy: 'position', targetEntity: Career::class)]
    private Collection $careers;

    #[ORM\Column(type: 'datetime', nullable: true)]
    private null|\DateTimeInterface $activationDate = null;

    public function __construct()
    {
        $this->translations = new ArrayCollection();
        $this->careers = new ArrayCollection();
    }

    public function getImage(): Image
    {
        return $this->image;
    }

    public function setImage(Image $image): self
    {
        $this->image = $image;

        return $this;
    }

    /**
     * @return Collection<int, CareerDescriptionTranslation>
     */
    public function getCareerDescriptionTranslations(): Collection
    {
        return $this->translations;
    }

    public function addCareerDescriptionTranslation(CareerDescriptionTranslation $careerDescriptionTranslation): self
    {
        if (!$this->translations->contains($careerDescriptionTranslation)) {
            $this->translations[] = $careerDescriptionTranslation;
            $careerDescriptionTranslation->setCareerDescription($this);
        }

        return $this;
    }

    public function removeCareerDescriptionTranslation(CareerDescriptionTranslation $careerDescriptionTranslation): self
    {
        if ($this->translations->contains($careerDescriptionTranslation)) {
            $this->translations->removeElement($careerDescriptionTranslation);
            // set the owning side to null (unless already changed)
            if ($careerDescriptionTranslation->getCareerDescription() === $this) {
                $careerDescriptionTranslation->setCareerDescription(null);
            }
        }

        return $this;
    }

    /**
     * @deprecated
     * @param string $locale
     * @return CareerDescriptionTranslation
     */
    public function getTranslationByLocale(string $locale): CareerDescriptionTranslation
    {
        return $this->translations->filter(function ($trans) use ($locale) {
            /** @var CareerDescriptionTranslation $trans */
            return $trans->getLocale() == $locale;
        })->first();
    }

    /**
     * @return Collection<int, Career>
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
        }

        return $this;
    }

    public function getActivationDate(): null|\DateTimeInterface
    {
        return $this->activationDate;
    }

    public function setActivationDate(\DateTimeInterface $activationDate): self
    {
        $this->activationDate = $activationDate;

        return $this;
    }
}
