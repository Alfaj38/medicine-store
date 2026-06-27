<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->singleton('data_scope', function ($app) {
            return new \App\Services\DataScopeService();
        });
        
        $this->app->singleton('feature_manager', function ($app) {
            return new \App\Services\FeatureManager();
        });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        \Illuminate\Support\Facades\Schema::defaultStringLength(191);
        \App\Models\SubscriptionPayment::observe(\App\Observers\SubscriptionPaymentObserver::class);
    }
}
