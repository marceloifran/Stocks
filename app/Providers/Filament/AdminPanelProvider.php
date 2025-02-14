<?php

namespace App\Providers\Filament;

use Filament\Pages;
use Filament\Panel;
use Filament\Widgets;
use Filament\PanelProvider;
use App\Filament\Pages\Auth\Login;
use Filament\Support\Colors\Color;
use Hasnayeen\Themes\ThemesPlugin;
use Illuminate\Support\HtmlString;
use Filament\View\PanelsRenderHook;
use Filament\Http\Middleware\Authenticate;
use Hasnayeen\Themes\Http\Middleware\SetTheme;
use App\Filament\Pages\Auth\Login as AuthLogin;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\Cookie\Middleware\EncryptCookies;
use AssistantEngine\Filament\FilamentAssistantPlugin;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\AuthenticateSession;
use Illuminate\View\Middleware\ShareErrorsFromSession;
use Swis\Filament\Backgrounds\ImageProviders\MyImages;
use BezhanSalleh\FilamentLanguageSwitch\LanguageSwitch;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Swis\Filament\Backgrounds\FilamentBackgroundsPlugin;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Leandrocfe\FilamentApexCharts\FilamentApexChartsPlugin;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;

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
                    'Stocks',
                    'Administrativo',
                ]
            )
            ->databaseNotifications()
            ->login()
            ->sidebarCollapsibleOnDesktop()
            ->emailVerification()
            ->profile()
            ->login(Login::class)
            // ->colors([
            // 'danger' =>'#d2e6ff',
            // 'gray' => Color::Gray,
            // 'info' =>'#33FF5E',
            // 'primary' =>'#9BBEC8',
            // 'success' =>'#9BBEC8',
            // 'warning' =>'#E69315',
            // ])
            ->discoverResources(in: app_path('Filament/Resources'), for: 'App\\Filament\\Resources')
            ->discoverPages(in: app_path('Filament/Pages'), for: 'App\\Filament\\Pages')
            ->pages([
                Pages\Dashboard::class,
            ])
            ->discoverWidgets(in: app_path('Filament/Widgets'), for: 'App\\Filament\\Widgets')
            ->widgets([
                //  Widgets\AccountWidget::class,
                // Widgets\FilamentInfoWidget::class,
            ])
            ->middleware([
                EncryptCookies::class,
                \Hasnayeen\Themes\Http\Middleware\SetTheme::class,
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
                fn() => new HtmlString('
                        <p>
                            Powered by
                            <a
                                href="https://www.linkedin.com/in/marcelo-ifran-singh-79a14b21a/"
                                target="_blank"
                            >
                                Ifsin Tech 
                            </a>
                        </p>
                    '),
            )
            ->plugins([
                \Hasnayeen\Themes\ThemesPlugin::make(),
                // FilamentBackgroundsPlugin::make()
                // ->imageProvider(
                //     MyImages::make()
                //         ->directory('images/backgrounds')
                // ),
                // FilamentAssistantPlugin::make(),
                FilamentApexChartsPlugin::make()
            ]);
        LanguageSwitch::configureUsing(function (LanguageSwitch $switch) {
            $switch
                ->locales(['ar', 'en', 'fr']); // also accepts a closure
        });
    }
}
