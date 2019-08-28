<?php

namespace Yupmin\Worker\Job;

use Illuminate\Contracts\Container\Container;

class JobFactory
{
    /** @var Container */
    protected $app;

    /** @var array */
    protected $config;

    /** @var string */
    protected $jobName;

    /**
     * JobFactory constructor.
     * @param Container $app
     * @param $config
     */
    public function __construct(Container $app, $config)
    {
        $this->app = $app;
        $this->config = $config;
    }

    /**
     * @param string $jobName
     * @return JobInterface
     * @throws JobException
     * @throws \Illuminate\Contracts\Container\BindingResolutionException
     */
    public function build(string $jobName): JobInterface
    {
        $jobNames = array_keys($this->config['worker.jobs']);
        if (!in_array($jobName, $jobNames)) {
            throw new JobException('Not supported job.');
        }

        $this->jobName = $jobName;

        return $this->app->make($this->config["worker.jobs.{$jobName}.job_class"]);
    }

    public function getQueueName(): string
    {
        return $this->config["worker.jobs.{$this->jobName}.queue_name"];
    }
}
