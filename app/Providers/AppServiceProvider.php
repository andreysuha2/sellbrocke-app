<?php

namespace App\Providers;

use App\Console\Commands\ModelMakeCommand;
use App\Models\Category;
use App\Models\Defect;
use App\Models\Device;
use App\Observers\CategoryObserver;
use App\Observers\DefectObserver;
use App\Observers\DeviceObserver;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->extend('command.model.make', function ($command, $app) {
            return new ModelMakeCommand($app['files']);
        });
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        Category::observe(CategoryObserver::class);
        Device::observe(DeviceObserver::class);
        Defect::observe(DefectObserver::class);
    }
}
