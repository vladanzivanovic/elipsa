<?php

declare(strict_types=1);

namespace App\Model;

use Swift_FileStream;

class EmailModel
{
    public const SCRIPT_USER_REGISTRATION = 'USER_REGISTRATION';
    public const SCRIPT_USER_ORDERED = 'USER_ORDERED';
    public const SCRIPT_ORDER_STATUS_CHANGE = 'ORDER_STATUS_CHANGE';
    public const SCRIPT_USER_RESET_PASSWORD = 'USER_RESET_PASSWORD';
    public const SCRIPT_CONTACT_US = 'CONTACT_US';
    public const SCRIPT_LOYALTY = 'LOYALTY';
    public const SCRIPT_COLLABORATOR = 'COLLABORATOR';
    public const SCRIPT_CAREER = 'CAREER';

    public const SCRIPT_PRODUCT_SIZE_AVAILABLE = 'PRODUCT_SIZE_AVAILABLE';

    private string $to;

    private string $toName = '';

    private string $template;

    private string $replyTo;

    private string $replyToName = '';

    private ?string $subject = null;

    private array $templateData;

    /**
     * @var Swift_FileStream[]|null
     */
    private ?array $attachments = null;

    private string $status;

    private string $code;

    private string $script;

    private ?string $errorMsg = null;

    private string $from;

    private string $fromName;

    /**
     * @param string $to
     */
    public function setTo(string $to): void
    {
        $this->to = $to;
    }

    /**
     * @return string
     */
    public function getTo(): string
    {
        return $this->to;
    }

    /**
     * @param string $toName
     */
    public function setToName(string $toName): void
    {
        $this->toName = $toName;
    }

    /**
     * @return string
     */
    public function getToName(): string
    {
        return $this->toName;
    }

    /**
     * @param string $template
     */
    public function setTemplate(string $template): void
    {
        $this->template = $template.'.html.twig';
    }

    /**
     * @return string
     */
    public function getTemplate(): string
    {
        return $this->template;
    }

    /**
     * @param string $replyTo
     */
    public function setReplyTo(string $replyTo): void
    {
        $this->replyTo = $replyTo;
    }

    /**
     * @return string
     */
    public function getReplyTo(): string
    {
        return $this->replyTo;
    }

    /**
     * @param string $replyToName
     */
    public function setReplyToName(string $replyToName): void
    {
        $this->replyToName = $replyToName;
    }

    /**
     * @return string
     */
    public function getReplyToName(): string
    {
        return $this->replyToName;
    }

    /**
     * @param string $subject
     */
    public function setSubject(string $subject): void
    {
        $this->subject = $subject;
    }

    /**
     * @return string
     */
    public function getSubject(): ?string
    {
        return $this->subject;
    }

    /**
     * @return bool
     */
    public function hasSubject(): bool
    {
        return is_string($this->subject);
    }

    /**
     * @param array $templateData
     */
    public function setTemplateData(array $templateData): void
    {
        $this->templateData = $templateData;
    }

    /**
     * @return array
     */
    public function getTemplateData(): array
    {
        return $this->templateData;
    }

    /**
     * @param Swift_FileStream[] $attachments
     */
    public function setAttachments(array $attachments): void
    {
        $this->attachments = $attachments;
    }

    /**
     * @return Swift_FileStream[]|null
     */
    public function getAttachments(): ?array
    {
        return $this->attachments;
    }

    /**
     * @param string $status
     */
    public function setStatus(string $status): void
    {
        $this->status = $status;
    }

    /**
     * @return string
     */
    public function getStatus(): string
    {
        return $this->status;
    }

    /**
     * @param string $code
     */
    public function setCode(string $code): void
    {
        $this->code = $code;
    }

    /**
     * @return string
     */
    public function getCode(): string
    {
        return $this->code;
    }

    /**
     * @param string $script
     */
    public function setScript(string $script): void
    {
        $this->script = $script;
    }

    /**
     * @return string
     */
    public function getScript(): string
    {
        return $this->script;
    }

    /**
     * @param string $error
     */
    public function setErrorMsg(string $error): void
    {
        $this->errorMsg = $error;
    }

    /**
     * @return string|null
     */
    public function getErrorMsg(): ?string
    {
        return $this->errorMsg;
    }

    /**
     * @return array
     */
    public function _toArray(): array
    {
        return call_user_func('get_object_vars', $this);
    }

    /**
     * @return string
     */
    public function getFrom(): string
    {
        return $this->from;
    }

    /**
     * @param string $from
     */
    public function setFrom(string $from): void
    {
        $this->from = $from;
    }

    /**
     * @return string
     */
    public function getFromName(): string
    {
        return $this->fromName;
    }

    /**
     * @param string $fromName
     */
    public function setFromName(string $fromName): void
    {
        $this->fromName = $fromName;
    }
}
