<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Models\User;
use App\Observers\UserObserver;

// Interfaces
use App\Contracts\UserRepositoryInterface;
use App\Contracts\CotizacionRepositoryInterface;
use App\Contracts\ProductoRepositoryInterface;
use App\Contracts\UserServiceInterface;
use App\Contracts\CotizacionServiceInterface;

// Implementations
use App\Repositories\UserRepository;
use App\Repositories\CotizacionRepository;
use App\Repositories\ProductoRepository;
use App\Services\UserService;
use App\Services\CotizacionService;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        // Registrar Repositories
        $this->app->bind(UserRepositoryInterface::class, UserRepository::class);
        $this->app->bind(CotizacionRepositoryInterface::class, CotizacionRepository::class);
        $this->app->bind(ProductoRepositoryInterface::class, ProductoRepository::class);

        // Registrar Services
        $this->app->bind(UserServiceInterface::class, UserService::class);
        $this->app->bind(CotizacionServiceInterface::class, CotizacionService::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        User::observe(UserObserver::class);
    }
}
