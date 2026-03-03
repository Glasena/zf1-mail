<?php

namespace Application\Modules\Mail\Services;

use Application\Modules\Mail\DTOs\SendMailDTO;
use Application\Modules\Mail\Entities\Mail;
use Doctrine\ORM\EntityManager;
use Zend_Mail;

class MailService
{

    private EntityManager $em;

    public function __construct(EntityManager $em)
    {
        $this->em = $em;
    }

    public function sendMail(SendMailDTO $sendMailDTO)
    {

        $mail = new Mail();
        $mail->setRecipient($sendMailDTO->recipient);
        $mail->setSubject($sendMailDTO->subject);
        $mail->setBody($sendMailDTO->body);
        $mail->setStatus(Mail::STATUS_TYPES['draft']);

        try {
            $zendMail = new Zend_Mail('UTF-8');
            $zendMail->setFrom(getenv('MAIL_FROM'), 'ZF1');
            $zendMail->addTo($sendMailDTO->recipient);
            $zendMail->setSubject($sendMailDTO->subject);
            $zendMail->setBodyText($sendMailDTO->body);
            $zendMail->send();
            $mail->setStatus(Mail::STATUS_TYPES['sent']);
            $this->em->persist($mail);
        } catch (\Throwable $th) {
            $mail->setStatus(Mail::STATUS_TYPES['failed']);
            $this->em->persist($mail);
            throw $th;
        } finally {
            $this->em->flush();
        }

    }
}