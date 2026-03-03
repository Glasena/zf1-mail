<?php

namespace Application\Modules\Mail\DTOs;

use InvalidArgumentException;

class SendMailDTO
{
    public readonly string $subject;
    public readonly string $body;
    public readonly string $recipient;

    public function __construct(array $data)
    {

        $dataSubject = $data['subject'] ?? null;

        if (is_string($dataSubject)) {
            $this->subject = $dataSubject;
        } else {
            throw new InvalidArgumentException('Subject must be a string');
        }

        $dataBody = $data['body'] ?? null;

        if (is_string($dataBody)) {
            $this->body = $dataBody;
        } else {
            throw new InvalidArgumentException('Body must be a string');
        }

        $dataRecipient = $data['recipient'] ?? null;

        if (is_string($dataRecipient) && filter_var($dataRecipient, FILTER_VALIDATE_EMAIL)) {
            $this->recipient = $dataRecipient;
        } else {
            throw new InvalidArgumentException('Recipient must be a valid email address');
        }

    }

}