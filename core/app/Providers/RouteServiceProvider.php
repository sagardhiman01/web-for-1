<?php

namespace App\Providers;

use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Route;

class RouteServiceProvider extends ServiceProvider
{
    /**
     * The path to the "home" route for your application.
     *
     * This is used by Laravel authentication to redirect users after login.
     *
     * @var string
     */

    protected $namespace = 'App\Http\Controllers';

    /**
     * Define your route model bindings, pattern filters, etc.
     *
     * @return void
     */
    public function boot()
    {
        $this->configureRateLimiting();

        $this->routes(function () {
            Route::namespace($this->namespace)->group(function(){
                Route::prefix('api')
                    ->middleware(['api','maintenance'])
                    ->group(base_path('routes/api.php'));

                Route::middleware(['web','maintenance'])
                    ->namespace('Gateway')
                    ->prefix('ipn')
                    ->name('ipn.')
                    ->group(base_path('routes/ipn.php'));

                Route::middleware(['web'])
                    ->namespace('Admin')
                    ->prefix('admin')
                    ->name('admin.')
                    ->group(base_path('routes/admin.php'));

                Route::middleware(['web','maintenance'])
                    ->prefix('user')
                    ->group(base_path('routes/user.php'));

                Route::middleware(['web','maintenance'])
                    ->group(base_path('routes/web.php'));

            });

        });

        Route::get('maintenance-mode','App\Http\Controllers\SiteController@maintenance')->name('maintenance');
    }

    /**
     * Configure the rate limiters for the application.
     *
     * @return void
     */
    protected function configureRateLimiting()
    {
        RateLimiter::for('api', function (Request $request) {
            return Limit::perMinute(60)->by($request->user()?->id ?: $request->ip());
        });

        RateLimiter::for('login', function (Request $request) {
            $identifier = strtolower((string) ($request->input('username') ?: $request->input('email') ?: 'guest'));
            return [
                Limit::perMinute(25)->by($identifier . '|' . $request->ip()),
                Limit::perMinute(120)->by($request->ip()),
            ];
        });

        RateLimiter::for('register', function (Request $request) {
            return Limit::perMinute(20)->by($request->ip());
        });

        RateLimiter::for('contact', function (Request $request) {
            return Limit::perMinute(15)->by($request->ip());
        });

        RateLimiter::for('chatbot', function (Request $request) {
            return [
                Limit::perMinute(50)->by(($request->user()?->id ?: 'guest') . '|' . $request->ip()),
                Limit::perMinute(200)->by($request->ip()),
            ];
        });

        // Financial Operations - Strict limits
        RateLimiter::for('withdraw', function (Request $request) {
            return [
                Limit::perMinute(5)->by($request->user()?->id ?: $request->ip()),
                Limit::perHour(20)->by($request->user()?->id ?: $request->ip()),
            ];
        });

        RateLimiter::for('deposit', function (Request $request) {
            return [
                Limit::perMinute(10)->by($request->user()?->id ?: $request->ip()),
                Limit::perHour(30)->by($request->user()?->id ?: $request->ip()),
            ];
        });

        RateLimiter::for('invest', function (Request $request) {
            return [
                Limit::perMinute(5)->by($request->user()?->id ?: $request->ip()),
                Limit::perHour(20)->by($request->user()?->id ?: $request->ip()),
            ];
        });

        RateLimiter::for('transfer', function (Request $request) {
            return [
                Limit::perMinute(3)->by($request->user()?->id ?: $request->ip()),
                Limit::perHour(10)->by($request->user()?->id ?: $request->ip()),
            ];
        });

        RateLimiter::for('nft', function (Request $request) {
            return [
                Limit::perMinute(10)->by($request->user()?->id ?: $request->ip()),
                Limit::perHour(50)->by($request->user()?->id ?: $request->ip()),
            ];
        });

        RateLimiter::for('investment_code', function (Request $request) {
            return [
                Limit::perMinute(5)->by($request->user()?->id ?: $request->ip()),
                Limit::perHour(15)->by($request->user()?->id ?: $request->ip()),
            ];
        });
    }
}
