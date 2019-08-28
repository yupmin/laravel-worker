<?php

namespace Yupmin\Worker;

use ErrorException;
use Exception;
use Illuminate\Contracts\Events\Dispatcher as EventDispatcher;
use PhpAmqpLib\Channel\AMQPChannel;
use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;
use Yupmin\Worker\Events\WorkerFailed;
use Yupmin\Worker\Events\WorkerRunning;
use Yupmin\Worker\Job\JobFactory;

class AmqpBasicWorker implements WorkerInterface
{
    use WorkerTrait;

    /** @var AMQPStreamConnection */
    protected $connection;

    /** @var bool */
    protected $durable;

    /**
     * AmqpBasicWorker constructor.
     * @param JobFactory $jobFactory
     * @param EventDispatcher $eventDispatcher
     * @param array $config
     */
    public function __construct(
        JobFactory $jobFactory,
        EventDispatcher $eventDispatcher,
        array $config
    ) {
        $this->jobFactory = $jobFactory;
        $this->eventDispatcher = $eventDispatcher;
        $this->config = $config;
        $this->connection = new AMQPStreamConnection(
            $config['host'],
            $config['port'],
            $config['user'],
            $config['password'],
            $config['vhost']
        );
        $this->durable = $config['durable'];
    }

    /**
     * @throws Job\JobException
     * @throws \Illuminate\Contracts\Container\BindingResolutionException
     */
    public function sendMessage(string $message): void
    {
        $this->jobFactory->build($this->jobName);

        $channel = $this->connection->channel();

        $channel->queue_declare($this->jobFactory->getQueueName(), false, $this->durable, false, false);

        $properties = $this->durable
            ? ['delivery_mode' => AMQPMessage::DELIVERY_MODE_PERSISTENT]
            : [];
        $AMQPMessage = new AMQPMessage($message, $properties);
        $channel->basic_publish($AMQPMessage, '', $this->jobFactory->getQueueName());
    }

    /**
     * @throws Job\JobException
     * @throws \Illuminate\Contracts\Container\BindingResolutionException
     */
    public function run(): void
    {
        $job = $this->jobFactory->build($this->jobName);
        $this->eventDispatcher->dispatch(new WorkerRunning($this, $job));

        $channel = $this->connection->channel();
        $channel->queue_declare($this->jobFactory->getQueueName(), false, $this->durable, false, false);

        # $channel->basic_qos(null, 1, null);
        $channel->basic_consume(
            $this->jobFactory->getQueueName(),
            '',
            false,
            !$this->durable,
            false,
            false,
            function (AMQPMessage $AMQPMessage) use ($job) {
                $this->executeJob($job, $AMQPMessage->body);

                if ($this->durable) {
                    /** @var AMQPChannel $channel */
                    $channel = $AMQPMessage->delivery_info['channel'];
                    $channel->basic_ack($AMQPMessage->delivery_info['delivery_tag']);
                }
            }
        );

        try {
            while ($channel->is_consuming()) {
                $channel->wait();
            }
        } catch (Exception $exception) {
            $this->eventDispatcher->dispatch(new WorkerFailed($this, $job, $exception));
            throw $exception;
        }
    }
}
