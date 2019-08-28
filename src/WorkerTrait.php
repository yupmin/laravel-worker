<?php

namespace Yupmin\Worker;

use Closure;
use Illuminate\Contracts\Events\Dispatcher as EventDispatcher;
use Yupmin\Worker\Events\JobExecuted;
use Yupmin\Worker\Events\JobExecuting;
use Yupmin\Worker\Job\JobFactory;
use Yupmin\Worker\Job\JobInterface;

trait WorkerTrait
{
    /** @var EventDispatcher */
    protected $eventDispatcher;

    /** @var string */
    protected $jobName;

    /** @var JobFactory  */
    protected $jobFactory;

    /** @var array */
    protected $config;

    /** @var Closure */
    protected $consoleCallback;

    public function setJobName(string $jobName): void
    {
        $this->jobName = $jobName;
    }

    public function getJobName(): string
    {
        return $this->jobName;
    }

    public function setConsoleCallback(Closure $consoleCallback): void
    {
        $this->consoleCallback = $consoleCallback;
    }

    public function getConsoleCallback(): Closure
    {
        return $this->consoleCallback;
    }

    public function executeJob(JobInterface $job, string $message)
    {
        $this->eventDispatcher->dispatch(new JobExecuting($this, $job, $message));

        $job->setMessage($message);
        $job->execute();

        $this->eventDispatcher->dispatch(new JobExecuted($this, $job, $message));
    }
}
