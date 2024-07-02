<?php

namespace App\Entity;

use App\Entity\Resources\EntityInterface;
use App\Entity\Resources\ResourceTrait;
use App\Repository\ImageRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: ImageRepository::class)]
class Image implements EntityInterface
{
    use ResourceTrait;

    public const RELATED_TYPE_PRODUCT = 1;
    public const RELATED_TYPE_SLIDER = 2;
    public const RELATED_TYPE_BANNER = 3;
    public const RELATED_TYPE_LOCATION = 4;
    public const RELATED_TYPE_BLOG = 5;
    public const RELATED_TYPE_CATALOG = 6;
    public const RELATED_TYPE_COLLABORATOR = 7;
    public const RELATED_TYPE_DESCRIPTION = 8;
    public const RELATED_TYPE_CAREER = 9;

    public const DEVICE_DESKTOP = 'desktop';
    public const DEVICE_MOBILE = 'mobile';

    #[ORM\Column(type: 'string', length: 255)]
    private string $name;

    #[ORM\Column(type: 'string', length: 255)]
    private string $originalName;

    #[ORM\Column(type: 'smallint')]
    #[Assert\NotBlank]
    private int $relatedToType;

    #[ORM\Column(type: 'boolean')]
    private bool $isMain;

    /**
     * @var UploadedFile $file
     */
    #[Assert\Image(maxSize: '10M')]
    private UploadedFile $file;

    private bool $isDeleted = false;

    #[ORM\Column(type: 'string', length: 25, nullable: true)]
    private null|string $device = null;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private null|string $parentImage = null;

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getOriginalName(): string
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

    public function getIsMain(): bool
    {
        return $this->isMain;
    }

    public function setIsMain(bool $isMain): self
    {
        $this->isMain = $isMain;

        return $this;
    }

    public function getFile(): UploadedFile
    {
        return $this->file;
    }

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

    public function getDevice(): null|string
    {
        return $this->device;
    }

    public function setDevice(null|string $device): self
    {
        $this->device = $device;

        return $this;
    }

    public function getParentImage(): null|string
    {
        return $this->parentImage;
    }

    public function setParentImage(null|string $parentImage): self
    {
        $this->parentImage = $parentImage;

        return $this;
    }
}
