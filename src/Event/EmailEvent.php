<?php

declare(strict_types=1);

namespace App\Event;

use App\Model\EmailModel;
use Symfony\Contracts\EventDispatcher\Event;

final class EmailEvent extends Event
{
    public const SEND_EMAIL = 'send.email';

    protected \App\Model\EmailModel $emailModel;

    /**
     * EmailEvent constructor.
     */
    public function __construct(EmailModel $emailModel)
    {
        $this->emailModel = $emailModel;
    }

    public function getEmailModel(): EmailModel
    {
        return $this->emailModel;
    }
}