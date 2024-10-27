<?php

declare(strict_types=1);

namespace App\Exception;


use Ixopay\Client\Transaction\Result;

class PaymentRequestException extends \Exception implements TranslatorExceptionInterface
{
    use TranslationExceptionTrait;

    private null|Result $result = null;

    public function setResult(Result $result): void
    {
        $this->result = $result;
    }

    public function getResult(): null|Result
    {
        return $this->result;
    }
}
