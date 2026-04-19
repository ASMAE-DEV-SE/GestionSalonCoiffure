<?php

namespace App\Providers;

use App\Mail\Transport\BrevoTransport;
use Carbon\Carbon;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        if (config('app.env') === 'production') {
            URL::forceScheme('https');
        }

        Carbon::setLocale('fr');
        setlocale(LC_TIME, 'fr_FR.UTF-8', 'fr_FR', 'fra');

        Mail::extend('brevo', function () {
            return new BrevoTransport(config('mail.mailers.brevo.api_key'));
        });
    }
}
