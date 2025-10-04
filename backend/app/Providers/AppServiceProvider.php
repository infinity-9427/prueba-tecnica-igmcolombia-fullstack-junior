<?php

namespace App\Providers;

use App\Interfaces\ClientServiceInterface;
use App\Interfaces\InvoiceServiceInterface;
use App\Interfaces\UserServiceInterface;
use App\Models\Invoice;
use App\Observers\InvoiceObserver;
use App\Services\ClientService;
use App\Services\InvoiceService;
use App\Services\UserService;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(ClientServiceInterface::class, ClientService::class);
        $this->app->bind(InvoiceServiceInterface::class, InvoiceService::class);
        $this->app->bind(UserServiceInterface::class, UserService::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Register Invoice observer for automatic PDF generation
        Invoice::observe(InvoiceObserver::class);
    }
}
