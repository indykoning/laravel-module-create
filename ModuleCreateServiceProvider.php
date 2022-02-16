<?php

namespace IndyKoning\ModuleCreate;

use Illuminate\Support\ServiceProvider;
use IndyKoning\ModuleCreate\Console\Commands\MakeModule;

class ModuleCreateServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        if ($this->app->runningInConsole()) {
            $this->commands([
                MakeModule::class,
            ]);
        }
    }
}
