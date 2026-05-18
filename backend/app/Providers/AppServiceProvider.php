<?php

namespace App\Providers;

use Illuminate\Support\Facades\Broadcast;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        // Register broadcasting auth route with Sanctum (token-based) middleware
        // so Bearer-token clients can authorise private channels.
        Broadcast::routes(['middleware' => ['auth:sanctum']]);
    }
}
