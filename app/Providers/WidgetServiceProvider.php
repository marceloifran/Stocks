<?php

namespace App\Providers;

use App\Filament\Widgets\EmissionsByMonthChart;
use App\Filament\Widgets\EmissionsDistributionChart;
use App\Filament\Widgets\YearlyEmissionsComparisonChart;
use App\Filament\Widgets\EmissionsForecastChart;
use Filament\Facades\Filament;
use Filament\Navigation\NavigationGroup;
use Illuminate\Support\ServiceProvider;

class WidgetServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        // Registrar un hook para ocultar widgets según el rol
        Filament::serving(function () {
            if (auth()->check() && auth()->user()->hasRole('superadmin')) {
                // Ocultar los recursos relacionados con huella de carbono
                Filament::registerRenderHook(
                    'panels::sidebar.start',
                    fn(): string => '
                        <script>
                            document.addEventListener("DOMContentLoaded", function() {
                                // Ocultar elementos de Huella de Carbono
                                const huellaItems = document.querySelectorAll("a[href*=\'huella\']");
                                huellaItems.forEach(function(item) {
                                    const navItem = item.closest("li");
                                    if (navItem) {
                                        navItem.style.display = "none";
                                    }
                                });

                                // Ocultar elementos de Parámetros
                                const paramItems = document.querySelectorAll("a[href*=\'parametro\']");
                                paramItems.forEach(function(item) {
                                    const navItem = item.closest("li");
                                    if (navItem) {
                                        navItem.style.display = "none";
                                    }
                                });

                                // Ocultar widgets del dashboard
                                setTimeout(function() {
                                    const widgets = document.querySelectorAll(".fi-widget");
                                    widgets.forEach(function(widget) {
                                        const title = widget.querySelector(".fi-widget-header");
                                        if (title && (
                                            title.textContent.includes("Emisiones") ||
                                            title.textContent.includes("Carbono") ||
                                            title.textContent.includes("Huella")
                                        )) {
                                            widget.style.display = "none";
                                        }
                                    });
                                }, 500);
                            });
                        </script>
                    '
                );
            }
        });
    }
}
