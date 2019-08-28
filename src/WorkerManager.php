<?php

declare(strict_types=1);

namespace Yupmin\Worker;

use Illuminate\Contracts\Events\Dispatcher as EventDispatcher;
use Illuminate\Redis\RedisManager;
use Illuminate\Support\Manager;
use Yupmin\Worker\Job\JobFactory;

class WorkerManager extends Manager
{
    public function getDefaultDriver()
    {
        return $this->app['config']['worker.driver'];
    }

    protected function getConfig($name)
    {
        return $this->app['config']["worker.drivers.{$name}"];
    }

    /**
     * @throws \Illuminate\Contracts\Container\BindingResolutionException
     */
    public function createRedisDriver()
    {
        $jobFactory = $this->app->make(JobFactory::class, [
            'app' => $this->app,
            'config' => $this->app['config'],
        ]);
        $redisManager = $this->app->make(RedisManager::class);
        $eventDispatcher = $this->app->make(EventDispatcher::class);
        $config = $this->getConfig('redis');

        return new RedisWorker($jobFactory, $redisManager, $eventDispatcher, $config);
    }

    public function createAmqpBasicDriver()
    {
        $jobFactory = $this->app->make(JobFactory::class);
        $eventDispatcher = $this->app->make(EventDispatcher::class);
        $config = $this->getConfig('amqp_basic');

        return new AmqpBasicWorker($jobFactory, $eventDispatcher, $config);
    }
}
