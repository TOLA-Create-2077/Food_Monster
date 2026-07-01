<?php

namespace App\Providers;

use Illuminate\Pagination\Paginator;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\URL; // 🛠️ ថែមមួយជួរនេះដើម្បីគ្រប់គ្រង HTTPS

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
        // រក្សាមុខងារចាស់របស់បងទុកដដែល
        Paginator::useBootstrapFive();

        // 🛠️ ដំណោះស្រាយ៖ បង្ខំឱ្យ Laravel ទាញយក CSS/JS តាម HTTPS ពេលនៅលើ Cloud (Railway)
        // វាដោះស្រាយបញ្ហាផ្ទាំង Dashboard ចេញតែអក្សរទទេៗបាន ១០០%
        if (config('app.env') === 'production' || env('APP_ENV') === 'production') {
            URL::forceScheme('https');
        }
    }
}