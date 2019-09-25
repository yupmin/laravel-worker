<?php

declare(strict_types=1);

namespace Yupmin\Worker;

use Exception;
use Illuminate\Contracts\Events\Dispatcher as EventDispatcher;
use Illuminate\Redis\RedisManager;
use Yupmin\Worker\Events\WorkerFailed;
use Yupmin\Worker\Events\WorkerRunning;
use Yupmin\Worker\Job\JobFactory;

class RedisWorker implements WorkerInterface
{
    use WorkerTrait;

    /** @var RedisManager */
    protected $redisManager;

    /**
     * RedisWorker constructor.
     * @param JobFactory $jobFactory
     * @param RedisManager $redisManager
     * @param EventDispatcher $eventDispatcher
     * @param array $config
     */
    public function __construct(
        JobFactory $jobFactory,
        RedisManager $redisManager,
        EventDispatcher $eventDispatcher,
        array $config
    ) {
        $this->jobFactory = $jobFactory;
        $this->redisManager = $redisManager;
        $this->eventDispatcher = $eventDispatcher;
        $this->config = $config;
    }

    /**
     * @throws Job\JobException
     * @throws \Illuminate\Contracts\Container\BindingResolutionException
     */
    public function sendMessage(string $message): void
    {
        $this->jobFactory->build($this->jobName);

        $this->redisManager->publish($this->jobFactory->getQueueName(), $message);
    }

    /**
     * @throws Job\JobException
     * @throws \Illuminate\Contracts\Container\BindingResolutionException
     * @throws Exception
     */
    public function run(): void
    {
        $job = $this->jobFactory->build($this->jobName);
        $this->eventDispatcher->dispatch(new WorkerRunning($this, $job));

        try {
            $this->redisManager->subscribe($this->jobFactory->getQueueName(), function ($message) use ($job) {
                $this->executeJob($job, $message);
            });
        } catch (Exception $exception) {
            $this->eventDispatcher->dispatch(new WorkerFailed($this, $job, $exception));
            throw $exception;
        }
    }
}
