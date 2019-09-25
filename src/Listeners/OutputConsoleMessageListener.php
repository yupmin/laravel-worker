<?php

declare(strict_types=1);

namespace Yupmin\Worker\Listeners;

use Closure;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Yupmin\Worker\Events as WorkerEvents;
use Yupmin\Worker\Job\JobInterface;
use Yupmin\Worker\WorkerInterface;

class OutputConsoleMessageListener
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    protected function consoleCallback(WorkerInterface $worker, string $style, string $message): void
    {
        $consoleCallback = $worker->getConsoleCallback();
        if ($consoleCallback instanceof Closure) {
            $consoleCallback($style, $message);
        }
    }

    /**
     * Handle the event.
     *
     * @param  object  $event
     * @return void
     */
    public function handle($event)
    {
        if ($event instanceof WorkerEvents\WorkerRunning) {
            /** @var WorkerInterface $worker */
            $worker = $event->worker;

            $this->consoleCallback($worker, 'info', "{$worker->getJobName()} \"worker running...\"");
        } elseif ($event instanceof WorkerEvents\WorkerFailed) {
            /** @var WorkerInterface $worker */
            $worker = $event->worker;
            /** @var \Exception $exception */
            $exception = $event->exception;

            $this->consoleCallback($worker, 'error', "{$worker->getJobName()} \"worker failed.\"");
        } elseif ($event instanceof WorkerEvents\JobExecuting
            || $event instanceof WorkerEvents\JobExecuted
        ) {
            /** @var WorkerInterface $worker */
            $worker = $event->worker;
            /** @var JobInterface $job */
            $job = $event->job;
            /** @var string $message */
            $message = $event->message;

            $jobAction = $event instanceof WorkerEvents\JobExecuting
                ? 'executing'
                : 'executed';

            $this->consoleCallback(
                $worker,
                'info',
                "{$worker->getJobName()} \"job is {$jobAction}.\" ".json_encode($message)
            );
        }
    }
}
