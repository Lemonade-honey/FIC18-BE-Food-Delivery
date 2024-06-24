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
        $this->app->bind(\App\Services\Interfaces\FileService::class, \App\Services\FileServiceImpl::class);
        $this->app->bind(\App\Services\Interfaces\RestorantService::class, \App\Services\RestorantServiceImpl::class);
        $this->app->bind(\App\Services\Interfaces\ProductService::class, \App\Services\ProductServiceImpl::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
