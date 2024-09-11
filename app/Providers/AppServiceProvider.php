<?php

namespace App\Providers;

use App\Models\Categories;
use App\Models\Customers;
use App\Models\Products;
use App\Models\User;
use App\Observers\CategoriesObserver;
use App\Observers\CustomerObserver;
use App\Observers\ProductObserver;
use App\Observers\UserObserver;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Categories::observe(CategoriesObserver::class);
        User::observe(UserObserver::class);
        Products::observe(ProductObserver::class);
        Customers::observe(CustomerObserver::class);
    }
}
