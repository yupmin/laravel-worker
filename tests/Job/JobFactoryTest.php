<?php

namespace Yupmin\Worker\Test\Job;

use Orchestra\Testbench\TestCase;
use Yupmin\Worker\Job\EchoJob;
use Yupmin\Worker\Job\JobFactory;
use Yupmin\Worker\Job\JobInterface;
use Yupmin\Worker\ServiceProvider as WorkerServiceProvider;

class JobFactoryTest extends TestCase
{
    /** @var JobFactory $jobFactory */
    protected $jobFactory;

    protected function setUp(): void
    {
        parent::setUp();

        $this->jobFactory = $this->app->make(JobFactory::class);
    }

    /**
     * @throws \Illuminate\Contracts\Container\BindingResolutionException
     * @throws \Yupmin\Worker\Job\JobException
     */
    public function test_build_echo_job()
    {
        /** @var JobInterface $job */
        $job = $this->jobFactory->build('echo');

        $this->assertInstanceOf(JobInterface::class, $job);
        $this->assertInstanceOf(EchoJob::class, $job);
    }

    protected function getPackageProviders($app)
    {
        return [
            WorkerServiceProvider::class,
        ];
    }

    /**
     * Define environment setup.
     *
     * @param  \Illuminate\Foundation\Application  $app
     * @return void
     */
    protected function getEnvironmentSetUp($app)
    {
        $app['config']->set('worker', [
            'jobs' => [
                'echo' => [
                    'job_class' => EchoJob::class,
                ],
            ],
        ]);
    }
}
