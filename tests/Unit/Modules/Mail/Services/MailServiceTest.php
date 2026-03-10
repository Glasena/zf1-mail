<?php

namespace Tests\Unit\Modules\Mail\Services;

use Application\Modules\Mail\DTOs\SendMailDTO;
use Application\Modules\Mail\Interfaces\MailerInterface;
use Application\Modules\Mail\Services\MailService;
use Doctrine\ORM\EntityManager;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class MailServiceTest extends TestCase
{
    private EntityManager&MockObject $em;
    private MailerInterface&MockObject $mailer;
    private MailService $mailService;

    protected function setUp(): void
    {
        $this->em = $this->createMock(EntityManager::class);
        $this->mailer = $this->createMock(MailerInterface::class);
        $this->mailService = new MailService($this->em, $this->mailer);
    }

    public function test_sends_mail_successfully(): void
    {

        $dto = new SendMailDTO([
            'recipient' => 'test@example.com',
            'subject' => 'Test',
            'body' => 'Hello',
        ]);

        $this->mailer->expects($this->once())
            ->method('sendMail')
            ->with($dto);

        $this->em->expects($this->once())
            ->method('flush');

        $this->mailService->sendMail($dto);

    }
    public function test_persists_failed_status_when_mailer_throws(): void
    {

        $dto = new SendMailDTO([
            'recipient' => 'test@example.com',
            'subject' => 'Test',
            'body' => 'Hello',
        ]);

        $this->mailer->method('sendMail')
            ->willThrowException(new \Exception('fail'));

        $this->em->expects($this->once())
            ->method('flush');

        $this->expectException(\Exception::class);

        $this->mailService->sendMail($dto);

    }

}