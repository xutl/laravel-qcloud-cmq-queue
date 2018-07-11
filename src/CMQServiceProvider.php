<?php

namespace XuTL\QCloud\Cmq\Queue;

use Illuminate\Support\ServiceProvider;

class CMQServiceProvider extends ServiceProvider
{
    /**
     * Indicates if loading of the provider is deferred.
     *
     * @var bool
     */
    protected $defer = false;


    public function boot()
    {
        $this->registerConnector($this->app['queue']);
        $this->commands('command.queue.cmq.flush');
    }

    /**
     * Register bindings in the container.
     *
     * @return void
     */
    public function register()
    {
        $this->registerCommand();
    }

    /**
     * Register the MNS queue connector.
     *
     * @param \Illuminate\Queue\QueueManager $manager
     *
     * @return void
     */
    protected function registerConnector($manager)
    {
        $manager->addConnector('cmq', function () {
            return new CMQConnector();
        });
    }

    /**
     * Register the cmq queue command.
     * @return void
     */
    private function registerCommand()
    {
        $this->app->singleton('command.queue.cmq.flush', function () {
            return new CMQFlushCommand();
        });
    }
}
