<?php

namespace Application\Commands;

use Application\Modules\Mail\DTOs\SendMailDTO;
use Application\Modules\Mail\Services\MailService;
use Doctrine\ORM\EntityManager;

class SendMailCommand extends AbstractQueueCommand
{

    protected EntityManager $em;

    protected function getQueueName(): string
    {
        return 'mail_queue';
    }

    public function __construct(EntityManager $em)
    {
        $this->em = $em;
        parent::__construct();
    }

    protected function handle(array $data): void
    {
        $mailService = new MailService($this->em);
        $sendMailDTO = new SendMailDTO($data);
        $mailService->sendMail($sendMailDTO);
    }

}
