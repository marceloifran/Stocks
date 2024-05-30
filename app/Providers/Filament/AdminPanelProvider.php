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
use Filament\View\PanelsRenderHook;
use Filament\Http\Middleware\Authenticate;
use Hasnayeen\Themes\Http\Middleware\SetTheme;
use App\Filament\Pages\Auth\Login as AuthLogin;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Afsakar\FilamentOtpLogin\FilamentOtpLoginPlugin;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\AuthenticateSession;
use Illuminate\View\Middleware\ShareErrorsFromSession;
use Saade\FilamentLaravelLog\FilamentLaravelLogPlugin;
use Swis\Filament\Backgrounds\ImageProviders\MyImages;
use BezhanSalleh\FilamentLanguageSwitch\LanguageSwitch;
use Kenepa\TranslationManager\TranslationManagerPlugin;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Swis\Filament\Backgrounds\FilamentBackgroundsPlugin;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use BezhanSalleh\FilamentLanguageSwitch\FilamentLanguageSwitchPlugin;
use Althinect\FilamentSpatieRolesPermissions\FilamentSpatieRolesPermissionsPlugin;

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
                    'Administrative',
                ]
            )
            ->databaseNotifications()
            ->login()
            ->sidebarCollapsibleOnDesktop()
            ->emailVerification()
            ->profile()
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
            )
            ->plugins([FilamentSpatieRolesPermissionsPlugin::make(),
            \Hasnayeen\Themes\ThemesPlugin::make(),
            // FilamentOtpLoginPlugin::make(),
            FilamentBackgroundsPlugin::make()
            ->imageProvider(
                MyImages::make()
                    ->directory('images/backgrounds')
            ),
            ]
            );
            LanguageSwitch::configureUsing(function (LanguageSwitch $switch) {
                $switch
                    ->locales(['ar','en','fr']); // also accepts a closure
            });
    }
}
