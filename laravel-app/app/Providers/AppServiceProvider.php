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
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        $compiled = env('VIEW_COMPILED_PATH');
        if ($compiled && !is_dir($compiled)) {
            @mkdir($compiled, 0777, true);
        }
    }
}
