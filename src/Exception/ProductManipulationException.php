<?php

declare(strict_types=1);

namespace App\Exception;

class ProductManipulationException extends \Exception implements TranslatorExceptionInterface
{
    use TranslationExceptionTrait;
}
