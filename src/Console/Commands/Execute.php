<?php

declare(strict_types=1);

namespace Yupmin\Worker\Console\Commands;

use Illuminate\Console\Command;
use Yupmin\Worker\Job\JobFactory;
use Yupmin\Worker\Job\JobInterface;
use Yupmin\Worker\WorkerInterface;
use Yupmin\Worker\WorkerManager;

class Execute extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'worker:execute {job-name} {message}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Execute Job';

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
     * @param JobFactory $jobFactory
     * @return mixed
     * @throws \Illuminate\Contracts\Container\BindingResolutionException
     * @throws \Yupmin\Worker\Job\JobException
     */
    public function handle(JobFactory $jobFactory)
    {
        $jobName = $this->argument('job-name');
        $message = $this->argument('message');

        /** @var JobInterface $worker */
        $job = $jobFactory->build($jobName);
        $job->setMessage($message);
        $job->execute();

        return 1;
    }
}
