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

        $this->mergeConfigFrom(__DIR__.'/config/module-create.php', 'module-create');

        $this->publishes([
            __DIR__.'/config/module-create.php' => config_path('module-create.php'),
        ], 'config');
    }
}
