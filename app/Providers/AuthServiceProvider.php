<?php

namespace App\Providers;

use App\Models\Company;
use App\Models\Category;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;
use Laravel\Passport\Passport;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     *
     * @var array
     */
    protected $policies = [
        // 'App\Model' => 'App\Policies\ModelPolicy',
    ];

    /**
     * Register any authentication / authorization services.
     *
     * @return void
     */
    public function boot()
    {
        $this->registerPolicies();
        Passport::routes();

        Gate::define("delete-category", function ($user, Category $category) {
            return !$category->descendants()->exists() && !$category->devices()->exists();
        });

        Gate::define("delete-company", function($user, Company $company) {
            return !$company->devices()->exists();
        });
    }
}
