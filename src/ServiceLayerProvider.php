<?php

namespace Madeweb\ServiceLayer;

use Illuminate\Support\ServiceProvider;
use Madeweb\ServiceLayer\Console\Commands\ServiceMakeCommand;

class ServiceLayerProvider extends ServiceProvider
{
    protected $commands = [
        ServiceMakeCommand::class
    ];

    public function boot()
    {
        $this->commands($this->commands);
    }

    public function register()
    {

    }
}
