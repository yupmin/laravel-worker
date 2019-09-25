<?php

declare(strict_types=1);

namespace Yupmin\Worker;

use Illuminate\Contracts\Events\Dispatcher as EventDispatcher;
use Illuminate\Support\ServiceProvider as BaseServiceProvider;
use Yupmin\Worker\Console\Commands as WorkerCommands;
use Yupmin\Worker\Listeners as WorkerListeners;
use Yupmin\Worker\Events as WorkerEvents;
use Yupmin\Worker\Job\JobFactory;

class ServiceProvider extends BaseServiceProvider
{
    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot(EventDispatcher $eventDispatcher)
    {
        $source = realpath(__DIR__.'/../config/worker.php');
        $this->mergeConfigFrom($source, 'worker');

        if ($this->app->runningInConsole()) {
            $this->publishes([$source => config_path('worker.php')]);

            $this->commands([
                WorkerCommands\Run::class,
                WorkerCommands\Send::class,
                WorkerCommands\Execute::class,
            ]);
        }

        $eventDispatcher->listen([
            WorkerEvents\WorkerRunning::class,
            WorkerEvents\WorkerFailed::class,
            WorkerEvents\JobExecuting::class,
            WorkerEvents\JobExecuted::class,
        ], WorkerListeners\OutputConsoleMessageListener::class);
    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton(WorkerManager::class, function ($app) {
            return new WorkerManager($app);
        });
        $this->app->singleton(JobFactory::class, function ($app) {
            $config = $app['config'];

            return new JobFactory($app, $config);
        });

        $this->app->alias(WorkerManager::class, 'yupmin.worker');
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides()
    {
        return [
            WorkerManager::class,
        ];
    }
}
