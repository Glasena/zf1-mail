<?php

namespace Application\Modules\Mail\Services;

use Application\Modules\Mail\DTOs\SendMailDTO;
use Application\Modules\Mail\Interfaces\MailerInterface;
use Zend_Mail;

class ZendMailService implements MailerInterface
{
    public function sendMail(SendMailDTO $sendMailDTO)
    {
        $zendMail = new Zend_Mail('UTF-8');
        $zendMail->setFrom(getenv('MAIL_FROM'), 'ZF1');
        $zendMail->addTo($sendMailDTO->recipient);
        $zendMail->setSubject($sendMailDTO->subject);
        $zendMail->setBodyText($sendMailDTO->body);
        $zendMail->send();

    }
}
