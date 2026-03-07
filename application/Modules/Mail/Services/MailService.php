<?php

namespace Application\Modules\Mail\Services;

use Application\Modules\Mail\DTOs\SendMailDTO;
use Application\Modules\Mail\Entities\Mail;
use Doctrine\ORM\EntityManager;
use PhpAmqpLib\Connection\AMQPStreamConnection;
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

    public function queueMail(SendMailDTO $sendMailDTO): void
    {
        $connection = new AMQPStreamConnection('rabbitmq', 5672, 'zf1_user', 'zf1_pass');
        $channel = $connection->channel();
        $channel->queue_declare('mail_queue', false, true, false, false);

        $data = [
            'recipient' => $sendMailDTO->recipient,
            'subject' => $sendMailDTO->subject,
            'body' => $sendMailDTO->body,
        ];

        $channel->basic_publish(
            new \PhpAmqpLib\Message\AMQPMessage(json_encode($data)),
            '',
            'mail_queue'
        );

        $channel->close();
        $connection->close();
    }
}