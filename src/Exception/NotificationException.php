<?php

declare(strict_types=1);

namespace App\Exception;

class NotificationException extends \Exception implements TranslatorExceptionInterface
{
    use TranslationExceptionTrait;
}
