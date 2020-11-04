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

    /**
     * @var NewsLetter
     */
    protected $newsLetter;

    /**
     * @var Loyalty|null
     */
    protected $loyalty;

    /**
     * @param NewsLetter   $newsLetter
     * @param Loyalty|null $loyalty
     */
    public function __construct(
        NewsLetter $newsLetter,
        ?Loyalty $loyalty = null
    ) {
        $this->newsLetter = $newsLetter;
        $this->loyalty = $loyalty;
    }

    /**
     * @return NewsLetter
     */
    public function getNewsLetter(): NewsLetter
    {
        return $this->newsLetter;
    }

    /**
     * @return Loyalty|null
     */
    public function getLoyalty(): ?Loyalty
    {
        return $this->loyalty;
    }
}