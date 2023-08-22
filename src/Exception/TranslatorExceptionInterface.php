<?php

declare(strict_types=1);

namespace App\Exception;

interface TranslatorExceptionInterface
{
    public function setParameters(array $parameters): void;

    public function getParameters(): array;

    public function setDomain(string $domain):void;

    public function getDomain(): string;
}
