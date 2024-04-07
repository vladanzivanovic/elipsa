<?php

declare(strict_types=1);

namespace App\Event;

use App\Entity\Loyalty;
use App\Entity\NewsLetter;
use Symfony\Contracts\EventDispatcher\Event;

final class NewsLetterEvent extends Event
{
    public const ADD_USER = 'newsletter.add_user';
    public const UPDATE_USER = 'newsletter.update_user';

    protected \App\Entity\NewsLetter $newsLetter;

    protected ?\App\Entity\Loyalty $loyalty;

    /**
     * @param Loyalty|null $loyalty
     */
    public function __construct(
        NewsLetter $newsLetter,
        ?Loyalty $loyalty = null
    ) {
        $this->newsLetter = $newsLetter;
        $this->loyalty = $loyalty;
    }

    public function getNewsLetter(): NewsLetter
    {
        return $this->newsLetter;
    }

    public function getLoyalty(): ?Loyalty
    {
        return $this->loyalty;
    }
}