<?php

declare(strict_types=1);

namespace App\Entity\Resources;

use Doctrine\ORM\Mapping as ORM;

trait LocaleTrait
{
    #[ORM\Column(type: 'string', length: 2)]
    private string $locale;

    public function getLocale(): string
    {
        return $this->locale;
    }

    public function setLocale(string $locale): self
    {
        $this->locale = $locale;

        return $this;
    }
}
