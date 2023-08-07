<?php

declare(strict_types=1);

namespace App\Exception;

final class OrderException extends \Exception implements TranslatorExceptionInterface
{
    use TranslationExceptionTrait;
}
