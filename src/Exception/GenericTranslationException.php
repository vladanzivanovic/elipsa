<?php

declare(strict_types=1);

namespace App\Exception;

use Symfony\Component\Translation\Translator;

class GenericTranslationException extends \Exception implements TranslatorExceptionInterface
{
    use TranslationExceptionTrait;
}
