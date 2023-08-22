<?php

declare(strict_types=1);

namespace App\Exception;

trait TranslationExceptionTrait
{
    private array $parameters = [];

    private string $domain = 'validators';

    public function setParameters(array $parameters): void
    {
        $this->parameters = $parameters;
    }

    public function getParameters(): array
    {
        return $this->parameters;
    }

    public function setDomain(string $domain):void
    {
        $this->domain = $domain;
    }

    public function getDomain(): string
    {
        return $this->domain;
    }
}
