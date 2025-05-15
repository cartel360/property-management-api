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
            \App\Repositories\Property\PropertyRepositoryInterface::class,
            \App\Repositories\Property\PropertyRepository::class,
        );
        $this->app->bind(
            \App\Repositories\Unit\UnitRepositoryInterface::class,
            \App\Repositories\Unit\UnitRepository::class,
        );
        $this->app->bind(
            \App\Repositories\Tenant\TenantRepositoryInterface::class,
            \App\Repositories\Tenant\TenantRepository::class,
        );
        $this->app->bind(
            \App\Repositories\Lease\LeaseRepositoryInterface::class,
            \App\Repositories\Lease\LeaseRepository::class,
        );

        $this->app->bind(
            \App\Repositories\Payment\PaymentRepositoryInterface::class,
            \App\Repositories\Payment\PaymentRepository::class,
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
