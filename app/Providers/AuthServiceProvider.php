<?php

namespace App\Providers;

use App\Models\Company;
use App\Models\Category;
use App\Models\Customer;
use App\Models\Merchant;
use App\Models\Order;
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

        Gate::define("get-orders", function (Merchant $merchant, Customer $customer) {
            return $merchant->customers->contains($customer);
        });

        Gate::define("get-order", function (Merchant $merchant, Customer $customer, Order $order) {
            $isAuthCustomer = $merchant->customers->contains($customer);
            return $isAuthCustomer ? $customer->orders->contains($order) : false;
        });

        Gate::define("create-order", function (Merchant $merchant, Customer $customer) {
            return $merchant->customers->contains($customer);
        });

        Gate::define("get-orders-by-customer", function (Merchant $merchant, Customer $customer) {
            return $merchant->customers->contains($customer);
        });
    }
}
