<?php

namespace Yupmin\Worker\Console\Commands;

use Illuminate\Console\Command;
use Yupmin\Worker\WorkerInterface;
use Yupmin\Worker\WorkerManager;

class Send extends Command
{
    use CommandTrait;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'worker:send {job-name} {message}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send Message to worker';

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
     * @return mixed
     */
    public function handle(WorkerManager $workerManager)
    {
        $jobName = $this->argument('job-name');
        $message = $this->argument('message');

        /** @var WorkerInterface $worker */
        $worker = $workerManager->driver();
        $worker->setJobName($jobName);
        $worker->sendMessage($message);

        $this->info($this->withTimestampNow()."{$jobName} \"sent message to worker.\"");

        return 1;
    }
}
