<?php

declare(strict_types=1);

namespace App\Entity\Resources;

use Doctrine\ORM\Mapping as ORM;

trait ResourceTrait
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private ?int $id = null;

    public function getId(): null|int
    {
        return $this->id;
    }
}
