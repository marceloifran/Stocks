<?php

namespace App\Providers\Filament;

use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Navigation\NavigationBuilder;
use Filament\Navigation\NavigationItem;
use Filament\Navigation\NavigationGroup;
use Filament\Pages;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Colors\Color;
use Filament\Widgets;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\AuthenticateSession;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\View\Middleware\ShareErrorsFromSession;
use App\Http\Middleware\SetTenantMiddleware;
use App\Filament\Resources\TenantResource;
use App\Filament\Resources\UserResource;
use App\Filament\Resources\HuellaCarbonoResource;
use App\Filament\Resources\HuellaCarbonoParametroResource;

class AdminPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->default()
            ->id('admin')
            ->path('admin')
            ->login()
            ->colors([
                'primary' => Color::Green,
            ])
            ->discoverResources(in: app_path('Filament/Resources'), for: 'App\\Filament\\Resources')
            ->discoverPages(in: app_path('Filament/Pages'), for: 'App\\Filament\\Pages')
            ->pages([
                Pages\Dashboard::class,
            ])
            ->discoverWidgets(in: app_path('Filament/Widgets'), for: 'App\\Filament\\Widgets')
            ->widgets([
                Widgets\AccountWidget::class,
                \App\Filament\Widgets\EmissionsDistributionChart::class,
                \App\Filament\Widgets\EmissionsByMonthChart::class,
            ])
            ->navigation(function (NavigationBuilder $builder): NavigationBuilder {
                // Personalizar navegación según el rol
                if (auth()->check() && auth()->user()->hasRole('superadmin')) {
                    // Para superadmin: solo mostrar usuarios y organizaciones
                    return $builder
                        ->items([
                            NavigationItem::make('Dashboard')
                                ->icon('heroicon-o-home')
                                ->isActiveWhen(fn(): bool => request()->routeIs('filament.admin.pages.dashboard'))
                                ->url(fn(): string => Pages\Dashboard::getUrl()),
                        ])
                        ->groups([
                            NavigationGroup::make('Administración')
                                ->items([
                                    NavigationItem::make('Organizaciones')
                                        ->icon('heroicon-o-building-office')
                                        ->isActiveWhen(fn(): bool => request()->routeIs('filament.admin.resources.tenants.*'))
                                        ->url(fn(): string => TenantResource::getUrl()),
                                    NavigationItem::make('Usuarios')
                                        ->icon('heroicon-o-users')
                                        ->isActiveWhen(fn(): bool => request()->routeIs('filament.admin.resources.users.*'))
                                        ->url(fn(): string => UserResource::getUrl()),
                                ]),
                        ]);
                } else {
                    // Para usuarios normales: acceso a funcionalidades de huella de carbono
                    return $builder
                        ->items([
                            NavigationItem::make('Dashboard')
                                ->icon('heroicon-o-home')
                                ->isActiveWhen(fn(): bool => request()->routeIs('filament.admin.pages.dashboard'))
                                ->url(fn(): string => Pages\Dashboard::getUrl()),
                        ])
                        ->groups([
                            NavigationGroup::make('Huella de Carbono')
                                ->items([
                                    NavigationItem::make('Registrar Huella')
                                        ->icon('heroicon-o-globe-alt')
                                        ->isActiveWhen(fn(): bool => request()->routeIs('filament.admin.resources.huella-carbonos.*'))
                                        ->url(fn(): string => HuellaCarbonoResource::getUrl()),

                                    NavigationItem::make('Parámetros')
                                        ->icon('heroicon-o-adjustments-horizontal')
                                        ->isActiveWhen(fn(): bool => request()->routeIs('filament.admin.resources.huella-carbono-parametros.*'))
                                        ->url(fn(): string => HuellaCarbonoParametroResource::getUrl()),
                                ]),
                        ]);
                }
            })
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
                SetTenantMiddleware::class,
            ]);
    }
}
