<?php
namespace App\Providers;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\URL;
use Carbon\Carbon;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void {}
    public function boot(): void
    {
        Carbon::setLocale('id');

        if (config('app.env') === 'production') {
            URL::forceScheme('https');
        }
    }
}
