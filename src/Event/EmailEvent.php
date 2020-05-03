<?php

declare(strict_types=1);

namespace App\Event;

use App\Model\EmailModel;
use Symfony\Contracts\EventDispatcher\Event;

final class EmailEvent extends Event
{
    public const SEND_EMAIL = 'send.email';

    /**
     * @var EmailModel
     */
    protected $emailModel;

    /**
     * EmailEvent constructor.
     *
     * @param EmailModel $emailModel
     */
    public function __construct(EmailModel $emailModel)
    {
        $this->emailModel = $emailModel;
    }

    /**
     * @return EmailModel
     */
    public function getEmailModel(): EmailModel
    {
        return $this->emailModel;
    }
}