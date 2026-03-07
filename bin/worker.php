<?php

define('APPLICATION_PATH', __DIR__ . '/../application');
define('APPLICATION_ENV', getenv('APPLICATION_ENV') ?: 'development');

require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/../config/load-env.php';

use Application\Modules\Mail\DTOs\SendMailDTO;
use Application\Modules\Mail\Services\MailService;
use Doctrine\ORM\EntityManager;
use PhpAmqpLib\Connection\AMQPStreamConnection;

$application = new Zend_Application(
    APPLICATION_ENV,
    APPLICATION_PATH . '/configs/application.ini'
);

$application->bootstrap('doctrine');

/** @var \Doctrine\ORM\EntityManager $em */
$em = Zend_Registry::get('doctrine.em');

$transport = new Zend_Mail_Transport_Smtp(getenv('MAIL_HOST'), [
    'port'     => (int) getenv('MAIL_PORT'),
    'auth'     => 'login',
    'username' => getenv('MAIL_USER'),
    'password' => getenv('MAIL_PASS'),
]);
Zend_Mail::setDefaultTransport($transport);

class SendMailWorker
{

    private AMQPStreamConnection $connection;
    private $channel;

    public function __construct(EntityManager $em)
    {
        $this->connection = new AMQPStreamConnection('rabbitmq', 5672, 'zf1_user', 'zf1_pass');
        $this->channel = $this->connection->channel();
        $this->channel->queue_declare('mail_queue', false, true, false, false);

        $callback = function ($message) use ($em) {
            $data = json_decode($message->body, true);
            echo "[worker] processing mail to: " . ($data['recipient'] ?? '?') . PHP_EOL;
            $mailService = new MailService($em);
            $sendMailDTO = new SendMailDTO($data);
            $mailService->sendMail($sendMailDTO);
            $message->ack();
            echo "[worker] done" . PHP_EOL;
        };

        $this->channel->basic_consume('mail_queue', '', false, false, false, false, $callback);

        while ($this->channel->is_consuming()) {
            $this->channel->wait();
        }

    }

}

new SendMailWorker($em);
