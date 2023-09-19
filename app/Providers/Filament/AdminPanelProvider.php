<?php

namespace App\Providers\Filament;

use login;
use Filament\Pages;
use Filament\Panel;
use Filament\Widgets;
use Filament\PanelProvider;
use Filament\Support\Colors\Color;
use Hasnayeen\Themes\ThemesPlugin;
use Illuminate\Support\HtmlString;
use Filament\Http\Middleware\Authenticate;
use pxlrbt\FilamentSpotlight\SpotlightPlugin;
use Hasnayeen\Themes\Http\Middleware\SetTheme;
use App\Filament\Pages\Auth\Login as AuthLogin;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\AuthenticateSession;
use Illuminate\View\Middleware\ShareErrorsFromSession;
use Saade\FilamentLaravelLog\FilamentLaravelLogPlugin;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use BezhanSalleh\FilamentLanguageSwitch\FilamentLanguageSwitchPlugin;

class AdminPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->default()
            ->id('admin')
            ->path('admin')
            ->navigationGroups(
                [
                    'Stocks' ,
                    'Administrativo',
                ]
            )
            ->databaseNotifications()
            ->login()
            ->plugins([
                SpotlightPlugin::make(),
               ThemesPlugin::make(),
                FilamentLaravelLogPlugin::make()
    ->navigationGroup('System Tools')
    ->navigationLabel('Logs')
    ->navigationIcon('heroicon-o-bug-ant')
    ->navigationSort(1)
    ->slug('logs')
            ])
            ->plugin(
                \Hasnayeen\Themes\ThemesPlugin::make(),
                FilamentLaravelLogPlugin::make()
                ->navigationGroup('System Tools')
                ->navigationLabel('Logs')
                ->navigationIcon('heroicon-o-bug-ant')
                ->navigationSort(1)
                ->slug('logs')

            )
            ->sidebarCollapsibleOnDesktop()
            ->registration()
            // ->passwordReset()
            ->emailVerification()
            ->profile()
            ->colors([
            'danger' =>'#E6151C',
            'gray' => Color::Gray,
            'info' =>'#33FF5E',
            'primary' =>'#33FF5E',
            'success' =>'#33FF5E',
            'warning' =>'#E69315',
            ])
            ->discoverResources(in: app_path('Filament/Resources'), for: 'App\\Filament\\Resources')
            ->discoverPages(in: app_path('Filament/Pages'), for: 'App\\Filament\\Pages')
            ->pages([
                Pages\Dashboard::class,
            ])
            ->discoverWidgets(in: app_path('Filament/Widgets'), for: 'App\\Filament\\Widgets')
            ->widgets([
                // Widgets\AccountWidget::class,
                // Widgets\FilamentInfoWidget::class,
            ])
            ->middleware([
                EncryptCookies::class,
                AddQueuedCookiesToResponse::class,
                StartSession::class,
                AuthenticateSession::class,
                ShareErrorsFromSession::class,
                VerifyCsrfToken::class,
                SubstituteBindings::class,
                DisableBladeIconComponents::class,
                DispatchServingFilamentEvent::class,
            ])
            ->authMiddleware([
                Authenticate::class,
            ])
            ->renderHook(
                'panels::page.end',
                fn () => new HtmlString('
                        <p>
                            Powered by
                            <a
                                href="https://www.linkedin.com/in/marcelo-ifran-singh-79a14b21a/"
                                target="_blank"
                            >
                                Marcelo Ifran Singh
                            </a>
                        </p>
                    '),
            );
    }
}
