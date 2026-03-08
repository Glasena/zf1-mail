<?php

namespace Application\Commands;

use Application\Commands\Interfaces\CommandInterface;
use PhpAmqpLib\Connection\AMQPStreamConnection;

abstract class AbstractQueueCommand implements CommandInterface
{

    private AMQPStreamConnection $connection;
    private $channel;

    abstract protected function getQueueName(): string;

    abstract protected function handle(array $data): void;

    public function __construct()
    {

        $callback = function ($message) {
            $data = json_decode($message->body, true);
            try {
                $this->handle($data);
                $message->ack();
            } catch (\Throwable $e) {
                $message->nack(requeue: true);
            }
        };

        $this->connection = new AMQPStreamConnection(
            getenv('RABBITMQ_HOST'),
            (int) getenv('RABBITMQ_PORT'),
            getenv('RABBITMQ_USER'),
            getenv('RABBITMQ_PASS')
        );
        $this->channel = $this->connection->channel();
        $this->channel->queue_declare($this->getQueueName(), false, true, false, false);
        $this->channel->basic_consume($this->getQueueName(), '', false, false, false, false, $callback);

    }

    public function execute(): void
    {
        while ($this->channel->is_consuming()) {
            $this->channel->wait();
        }
    }

}