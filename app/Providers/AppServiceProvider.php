<?php

namespace App\Providers;

use App\Models\Activity;
use App\Models\Category;
use App\Models\Product;
use App\Models\User;
use App\Observers\ActivitiesObserver;
use App\Observers\CategoriesObserver;
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
        Category::observe(CategoriesObserver::class);
        User::observe(UserObserver::class);
        Product::observe(ProductObserver::class);
        Activity::observe(ActivitiesObserver::class);
    }
}
