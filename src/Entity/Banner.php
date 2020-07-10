<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\BannerRepository")
 */
class Banner
{
    public const POSITION_HOME_LEFT = 1;
    public const POSITION_HOME_MIDDLE_UP = 2;
    public const POSITION_HOME_MIDDLE_DOWN = 3;
    public const POSITION_HOME_RIGHT = 4;

    public const TYPE_SPEED_LINKS = 1;
    public const TYPE_LOYALTY = 2;
    public const TYPE_NEWS_LETTER = 3;

    public const STATUS_PENDING = false;

    public const STATUS_ACTIVE = true;

    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\OneToOne(targetEntity="App\Entity\Image", cascade={"persist", "remove"})
     * @ORM\JoinColumn(nullable=false)
     */
    private $image;

    /**
     * @ORM\Column(type="boolean")
     */
    private $isActive;

    /**
     * @ORM\Column(type="integer")
     */
    private $position;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\BannerTranslation", mappedBy="banner", orphanRemoval=true, cascade={"persist", "remove"})
     */
    private $bannerTranslations;

    /**
     * @ORM\Column(type="smallint")
     */
    private $type;

    public function __construct()
    {
        $this->bannerTranslations = new ArrayCollection();
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

    public function getIsActive(): ?bool
    {
        return $this->isActive;
    }

    public function setIsActive(bool $isActive): self
    {
        $this->isActive = $isActive;

        return $this;
    }

    public function getPosition(): ?int
    {
        return $this->position;
    }

    public function setPosition(int $position): self
    {
        $this->position = $position;

        return $this;
    }

    /**
     * @return Collection|BannerTranslation[]
     */
    public function getBannerTranslations(): Collection
    {
        return $this->bannerTranslations;
    }

    public function addBannerTranslation(BannerTranslation $bannerTranslation): self
    {
        if (!$this->bannerTranslations->contains($bannerTranslation)) {
            $this->bannerTranslations[] = $bannerTranslation;
            $bannerTranslation->setBanner($this);
        }

        return $this;
    }

    public function removeBannerTranslation(BannerTranslation $bannerTranslation): self
    {
        if ($this->bannerTranslations->contains($bannerTranslation)) {
            $this->bannerTranslations->removeElement($bannerTranslation);
            // set the owning side to null (unless already changed)
            if ($bannerTranslation->getBanner() === $this) {
                $bannerTranslation->setBanner(null);
            }
        }

        return $this;
    }

    /**
     * @param string $locale
     *
     * @return BannerTranslation
     */
    public function getByLocale(string $locale): BannerTranslation
    {
        $trans = $this->bannerTranslations;

        $filteredTrans = $trans->filter(function ($bannerTrans) use ($locale) {
            /** @var BannerTranslation $bannerTrans */
            return $bannerTrans->getLocale() === $locale;
        });

        return $filteredTrans->first();
    }

    public function getType(): ?int
    {
        return $this->type;
    }

    public function setType(int $type): self
    {
        $this->type = $type;

        return $this;
    }
}
