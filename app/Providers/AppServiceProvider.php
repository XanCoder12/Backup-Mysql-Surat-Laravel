<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Http\Request; // ← ini yang kurang

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        
    }

    public function boot(): void
    {
        if (str_contains(request()->getHost(), 'ngrok-free.dev')) {
            \Illuminate\Support\Facades\URL::forceScheme('https');
        }
        RateLimiter::for('search', function (Request $request) {
            return Limit::perMinute(30)->by($request->user()?->id ?: $request->ip());
        });
        RateLimiter::for('global', function (Request $request) {
            return Limit::perMinute(60)->by($request->user()?->id ?: $request->ip());
        });
        RateLimiter::for('upload', function (Request $request) {
            return Limit::perMinute(20)->by($request->user()?->id ?: $request->ip());
        });

        RateLimiter::for('registration', function (Request $request) {
            return Limit::perMinute(3)->by($request->ip());
        });

        \Illuminate\Pagination\Paginator::defaultView('vendor.pagination.modern');
    }
}