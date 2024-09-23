<?php

namespace App\Providers;

use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Route;

class RouteServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        // This loads all the necessary routes, including the ones for Filament resources.
        $this->routes(function () {
            Route::middleware('web')
                ->group(base_path('routes/web.php'));

            Route::middleware('web')
                ->prefix('admin') // Ensure you're prefixing this for Filament
                ->group(base_path('routes/filament.php')); // Assuming you have a routes/filament.php file, or use the automatic Filament route loading
        });
    }
}
