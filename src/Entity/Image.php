<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass="App\Repository\ImageRepository")
 */
class Image
{
    public const RELATED_TYPE_PRODUCT = 1;
    public const RELATED_TYPE_SLIDER = 2;
    public const RELATED_TYPE_BANNER = 3;
    public const RELATED_TYPE_LOCATION = 4;
    public const RELATED_TYPE_BLOG = 5;
    public const RELATED_TYPE_CATALOG = 6;
    public const RELATED_TYPE_COLLABORATOR = 7;
    public const RELATED_TYPE_DESCRIPTION = 8;
    public const RELATED_TYPE_CAREER = 9;

    public const DEVICE_DESKTOP = 1;
    public const DEVICE_MOBILE = 2;

    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $name;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $originalName;

    /**
     * @ORM\Column(type="smallint")
     * @Assert\NotBlank()
     */
    private $relatedToType;

    /**
     * @ORM\Column(type="boolean")
     */
    private $isMain;

    /**
     * @var UploadedFile $file
     * @Assert\Image(maxSize="10M")
     */
    private $file;

    /**
     * @var bool
     */
    private $isDeleted = false;

    /**
     * @ORM\Column(type="smallint")
     */
    private $device;

    /**
     * @ORM\Column(type="string", nullable=true, length=255)
     */
    private $parentImage;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getOriginalName(): ?string
    {
        return $this->originalName;
    }

    public function setOriginalName(string $originalName): self
    {
        $this->originalName = $originalName;

        return $this;
    }

    public function setRelatedToType(int $relatedToType): self
    {
        $this->relatedToType = $relatedToType;

        return $this;
    }

    public function getIsMain(): ?bool
    {
        return $this->isMain;
    }

    public function setIsMain(bool $isMain): self
    {
        $this->isMain = $isMain;

        return $this;
    }

    /**
     * @return null|UploadedFile
     */
    public function getFile(): ?UploadedFile
    {
        return $this->file;
    }

    /**
     * @param UploadedFile $file
     *
     * @return Image
     */
    public function setFile(UploadedFile $file): self
    {
        $this->file = $file;

        return $this;
    }

    /**
     * @return bool
     */
    public function isDeleted(): bool
    {
        return $this->isDeleted;
    }

    /**
     * @param bool $isDeleted
     *
     * @return $this
     */
    public function setIsDeleted(bool $isDeleted): self
    {
        $this->isDeleted = $isDeleted;

        return $this;
    }

    public function getDevice(): ?int
    {
        return $this->device;
    }

    public function setDevice(int $device): self
    {
        $this->device = $device;

        return $this;
    }

    public function getParentImage(): ?string
    {
        return $this->parentImage;
    }

    public function setParentImage(?string $parentImage): self
    {
        $this->parentImage = $parentImage;

        return $this;
    }
}
