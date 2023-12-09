<?php

declare(strict_types=1);

namespace App\Exception;

final class CouponCheckerException extends \Exception implements TranslatorExceptionInterface
{
    use TranslationExceptionTrait;
}
