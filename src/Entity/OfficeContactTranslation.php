<?php

namespace App\Entity;

use App\Repository\OfficeContactTranslationRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=OfficeContactTranslationRepository::class)
 */
class OfficeContactTranslation
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $title;

    /**
     * @ORM\Column(type="string", length=3)
     */
    private $locale;

    /**
     * @ORM\ManyToOne(targetEntity=OfficeContact::class, inversedBy="officeContactTranslations")
     * @ORM\JoinColumn(nullable=false)
     */
    private $officeContact;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): self
    {
        $this->title = $title;

        return $this;
    }

    public function getLocale(): ?string
    {
        return $this->locale;
    }

    public function setLocale(string $locale): self
    {
        $this->locale = $locale;

        return $this;
    }

    public function getOfficeContact(): ?OfficeContact
    {
        return $this->officeContact;
    }

    public function setOfficeContact(?OfficeContact $officeContact): self
    {
        $this->officeContact = $officeContact;

        return $this;
    }
}
