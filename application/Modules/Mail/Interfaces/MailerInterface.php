<?php

namespace Application\Modules\Mail\Interfaces;

use Application\Modules\Mail\DTOs\SendMailDTO;

interface MailerInterface
{
    public function sendMail(SendMailDTO $sendMailDTO);
}