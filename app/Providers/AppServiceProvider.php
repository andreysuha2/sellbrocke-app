<?php

namespace App\Providers;

use App\Console\Commands\ModelMakeCommand;
use App\Models\Category;
use App\Models\Company;
use App\Models\Defect;
use App\Models\Device;
use App\Models\Order;
use App\Models\ProductGrid;
use App\Models\SearchSlug;
use App\Observers\CategoryObserver;
use App\Observers\CompanyObserver;
use App\Observers\DefectObserver;
use App\Observers\DeviceObserver;
use App\Observers\OrderObserver;
use App\Observers\ProductGridObserver;
use App\Observers\SearchSlugObserver;
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
        ProductGrid::observe(ProductGridObserver::class);
        Company::observe(CompanyObserver::class);
        SearchSlug::observe(SearchSlugObserver::class);
        Order::observe(OrderObserver::class);
    }
}
