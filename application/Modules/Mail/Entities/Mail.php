<?php

namespace Application\Modules\Mail\Entities;

use Application\Modules\Default\Entities\AbstractEntity;
use Doctrine\ORM\Mapping as ORM;


#[ORM\Entity]
#[ORM\Table(name: 'mails')]
class Mail extends AbstractEntity
{

    const STATUS_TYPES = [
        'draft' => 0,
        'sent' => 1,
        'failed' => 2,
    ];

    #[ORM\Column(name: 'subject', type: 'string')]
    private string $subject;

    #[ORM\Column(name: 'body', type: 'text')]
    private string $body;

    #[ORM\Column(name: 'recipient', type: 'string')]
    private string $recipient;

    #[ORM\Column(name: 'status', type: 'integer')]
    private int $status;

    public function getSubject(): string
    {
        return $this->subject;
    }

    public function getBody(): string
    {
        return $this->body;
    }

    public function getRecipient(): string
    {
        return $this->recipient;
    }

    public function setSubject(string $subject): self
    {
        $this->subject = $subject;
        return $this;
    }

    public function setBody(string $body): self
    {
        $this->body = $body;
        return $this;
    }

    public function setRecipient(string $recipient): self
    {
        $this->recipient = $recipient;
        return $this;
    }

    public function getStatus(): int
    {
        return $this->status;
    }

    public function setStatus(int $status): self
    {
        $this->status = $status;
        return $this;
    }

}