<?php

declare(strict_types=1);

namespace App\View;

use App\Exception\TranslatorExceptionInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

final class ExceptionView
{
    private TranslatorInterface $translator;

    public function __construct(
        TranslatorInterface $translator
    ) {
        $this->translator = $translator;
    }
    public function view(\Throwable $throwable, string $locale): array
    {
        $message = $throwable->getMessage();

        if ($throwable instanceof TranslatorExceptionInterface) {
            $message = $this->translator->trans(
                $throwable->getMessage(),
                $throwable->getParameters(),
                $throwable->getDomain(),
                $locale
            );
        }

        return [
            'message' => $message,
        ];
    }
}
