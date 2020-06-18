<?php

declare(strict_types=1);

namespace App\Event;

use App\Entity\NewsLetter;
use Symfony\Contracts\EventDispatcher\Event;

final class NewsLetterEvent extends Event
{
    public const ADD_USER = 'newsletter.add_user';

    /**
     * @var NewsLetter
     */
    protected $newsLetter;

    /**
     * EmailEvent constructor.
     *
     * @param NewsLetter $newsLetter
     */
    public function __construct(NewsLetter $newsLetter)
    {
        $this->newsLetter = $newsLetter;
    }

    /**
     * @return NewsLetter
     */
    public function getNewsLetter(): NewsLetter
    {
        return $this->newsLetter;
    }
}