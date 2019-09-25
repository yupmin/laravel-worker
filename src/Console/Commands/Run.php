<?php

declare(strict_types=1);

namespace Yupmin\Worker\Console\Commands;

use Illuminate\Console\Command;
use Yupmin\Worker\WorkerInterface;
use Yupmin\Worker\WorkerManager;

class Run extends Command
{
    use CommandTrait;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'worker:run {job-name}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Run Worker';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @param WorkerManager $workerManager
     * @return mixed
     */
    public function handle(WorkerManager $workerManager)
    {
        $jobName = $this->argument('job-name');

        /** @var WorkerInterface $worker */
        $worker = $workerManager->driver();
        $worker->setJobName($jobName);
        $worker->setConsoleCallback(function (string $style, string $message) {
            $this->{$style}($this->withTimestampNow()."{$message}");
        });
        $worker->run();

        return 1;
    }
}
