<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: \App\Repository\BlogHasTagsRepository::class)]
class BlogHasTags
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id;

    #[ORM\ManyToOne(targetEntity: \Blog::class, inversedBy: 'blogHasTags')]
    #[ORM\JoinColumn(nullable: false, referencedColumnName: 'id')]
    private $blog;

    #[ORM\ManyToOne(targetEntity: \App\Entity\Tags::class)]
    #[ORM\JoinColumn(nullable: false)]
    private Tags $tag;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getBlog(): ?Blog
    {
        return $this->blog;
    }

    public function setBlog(?Blog $blog): self
    {
        $this->blog = $blog;

        return $this;
    }

    public function getTag(): Tags
    {
        return $this->tag;
    }

    public function setTag(Tags $tag): self
    {
        $this->tag = $tag;

        return $this;
    }
}
