<?php

namespace App\Providers;

use App\Models\AdminNotification;
use App\Models\Deposit;
use App\Models\Frontend;
use App\Models\Language;
use App\Models\SupportTicket;
use App\Models\User;
use App\Models\Withdrawal;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {

    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {

        if ($this->app->runningInConsole()) {
            return;
        }

        try {
            $activeTemplate = activeTemplate();
            $general        = gs();
            $languageCacheTtl = (int) config('performance.language_cache_seconds', 3600);
            $languages = $languageCacheTtl > 0
                ? Cache::remember('languages:all', now()->addSeconds($languageCacheTtl), fn () => Language::all())
                : Language::all();

            $viewShare['general']            = $general;
            $viewShare['activeTemplate']     = $activeTemplate;
            $viewShare['activeTemplateTrue'] = activeTemplate(true);
            $viewShare['language']           = $languages;
            $viewShare['emptyMessage']       = 'Data not found';
            view()->share($viewShare);

            view()->composer('admin.partials.sidenav', function ($view) {
                $view->with([
                    'bannedUsersCount'           => User::banned()->count(),
                    'emailUnverifiedUsersCount'  => User::emailUnverified()->count(),
                    'mobileUnverifiedUsersCount' => User::mobileUnverified()->count(),
                    'kycUnverifiedUsersCount'    => User::kycUnverified()->count(),
                    'kycPendingUsersCount'       => User::kycPending()->count(),
                    'pendingTicketCount'         => SupportTicket::whereIN('status', [0, 2])->count(),
                    'pendingDepositsCount'       => Deposit::pending()->count(),
                    'pendingWithdrawCount'       => Withdrawal::pending()->count(),
                ]);
            });
        } catch (\Throwable $e) {
            // Silence DB errors during initial boot/server run
            view()->share([
                'general' => (object)[],
                'activeTemplate' => 'basic',
                'language' => collect([]),
                'emptyMessage' => 'Database connection failed. Please check your configuration.'
            ]);
        }

        try {
            view()->composer('admin.partials.topnav', function ($view) {
                $view->with([
                    'adminNotifications'     => AdminNotification::where('is_read', 0)->with('user')->orderBy('id', 'desc')->take(10)->get(),
                    'adminNotificationCount' => AdminNotification::where('is_read', 0)->count(),
                ]);
            });

            view()->composer('partials.seo', function ($view) {
                $seoCacheTtl = (int) config('performance.seo_cache_seconds', 1800);
                $seoCacheKey = 'seo.data.' . activeTemplateName();
                $seo = $seoCacheTtl > 0
                    ? Cache::remember($seoCacheKey, now()->addSeconds($seoCacheTtl), fn () => Frontend::where('template_name', activeTemplateName())->where('data_keys', 'seo.data')->first())
                    : Frontend::where('template_name', activeTemplateName())->where('data_keys', 'seo.data')->first();
                $view->with([
                    'seo' => $seo ? $seo->data_values : $seo,
                ]);
            });

            if ($general->force_ssl) {
                \URL::forceScheme('https');
            }
        } catch (\Throwable $e) {
            // Further silence errors
        }

        Paginator::useBootstrapFour();
    }
}
