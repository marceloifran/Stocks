<?php

namespace App\Providers;

use App\Models\User;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Gate;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\ServiceProvider;
use BezhanSalleh\FilamentLanguageSwitch\LanguageSwitch;

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
        // if (env('APP_ENV') === 'production') {
        //     URL::forceScheme('https');
        // }

        // Model::unguard();

        LanguageSwitch::configureUsing(function (LanguageSwitch $switch) {
            $switch
            ->visible(outsidePanels: true)
                ->locales(['es','en'])
                ->circular()
                ;
        });
    }
}
