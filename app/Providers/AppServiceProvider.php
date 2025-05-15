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
        $this->app->bind(
            \App\Repositories\PropertyRepositoryInterface::class,
            \App\Repositories\PropertyRepository::class,
            \App\Repositories\UnitRepositoryInterface::class,
            \App\Repositories\UnitRepository::class,
            \App\Repositories\TenantRepositoryInterface::class,
            \App\Repositories\TenantRepository::class,
            \App\Repositories\LeaseRepositoryInterface::class,
            \App\Repositories\LeaseRepository::class,
            \App\Repositories\PaymentRepositoryInterface::class,
            \App\Repositories\PaymentRepository::class,
            \App\Repositories\LeaseRepositoryInterface::class,
            \App\Repositories\LeaseRepository::class,
        );
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
