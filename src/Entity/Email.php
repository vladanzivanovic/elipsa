<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Gedmo\Timestampable\Traits\TimestampableEntity;

/**
 * @ORM\Entity(repositoryClass="App\Repository\EmailRepository")
 */
class Email
{
    const EMAIL_SUCCESS = 'SENT';
    const EMAIL_FAILED = 'FAILED';
    const EMAIL_SEEN = 'SEEN';

    /**
     * @var integer
     *
     * @ORM\Column(type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=255, nullable=false)
     */
    private $fromEmail;

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=255, nullable=false)
     */
    private $toEmail;

    /**
     * @var string
     *
     * @ORM\Column(type="text", nullable=true)
     */
    private $rawData;

    /**
     * @var string
     *
     * @ORM\Column(type="text", length=65535, nullable=true)
     */
    private $errorMessage;

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=150, nullable=false)
     */
    private $status;

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=200, nullable=false)
     */
    private $script;

    /**
     * @var string|null
     *
     * @ORM\Column(name="code", type="string", length=5)
     */
    private $code;

    /**
     * @var \DateTime
     * @Gedmo\Timestampable(on="create")
     * @ORM\Column(type="datetime")
     */
    private $createdAt;

    /**
     * Get id
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     *
     * @param string $fromEmail
     *
     * @return Email
     */
    public function setFromEmail($fromEmail): self
    {
        $this->fromEmail = $fromEmail;

        return $this;
    }

    /**
     * @return string
     */
    public function getFromEmail(): string
    {
        return $this->fromEmail;
    }

    /**
     * @param string $toEmail
     *
     * @return Email
     */
    public function setToEmail($toEmail): self
    {
        $this->toEmail = $toEmail;

        return $this;
    }

    /**
     * @return string
     */
    public function getToEmail(): string
    {
        return $this->toEmail;
    }

    /**
     * @param string $rawData
     *
     * @return Email
     */
    public function setRawData($rawData): self
    {
        $this->rawData = $rawData;

        return $this;
    }

    /**
     * @return string
     */
    public function getRawData(): string
    {
        return $this->rawData;
    }
    /**
     * @param string $errorMessage
     *
     * @return Email
     */
    public function setErrorMessage($errorMessage): self
    {
        $this->errorMessage = $errorMessage;

        return $this;
    }

    /**
     * @return string
     */
    public function getErrorMessage(): string
    {
        return $this->errorMessage;
    }

    /**
     * @param string $status
     *
     * @return Email
     */
    public function setStatus($status): self
    {
        $this->status = $status;

        return $this;
    }

    /**
     * @return string
     */
    public function getStatus(): string
    {
        return $this->status;
    }

    /**
     * @param string $script
     *
     * @return Email
     */
    public function setScript(string $script): self
    {
        $this->script = $script;

        return $this;
    }

    /**
     * @return string
     */
    public function getScript(): string
    {
        return $this->script;
    }

    /**
     * @param string|null $code
     *
     * @return Email
     */
    public function setCode(?string $code): self
    {
        $this->code = $code;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getCode(): ?string
    {
        return $this->code;
    }

    /**
     * @param \DateTime $createdAt
     *
     * @return Email
     */
    public function setCreatedAt(\DateTime $createdAt): self
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    /**
     * @return \DateTime
     */
    public function getCreatedAt(): \DateTime
    {
        return $this->createdAt;
    }
}
