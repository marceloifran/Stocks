<x-filament-panels::page>
    <div class="space-y-6">
        <!-- Obras Activas -->
        <div class="bg-white rounded-xl shadow dark:bg-gray-800 border border-gray-100 dark:border-gray-700">
            <div class="p-6 border-b border-gray-100 dark:border-gray-700">
                <h2 class="text-xl font-semibold flex items-center">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 mr-2 text-primary-500" fill="none"
                        viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                    </svg>
                    Obras Activas
                </h2>
            </div>
            <div class="p-6">
                @if ($obrasActivas->count() > 0)
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                        @foreach ($obrasActivas as $obra)
                            <div
                                class="border border-gray-200 dark:border-gray-700 rounded-lg p-4 hover:shadow-md transition-shadow">
                                <div class="flex justify-between items-start mb-3">
                                    <h3 class="font-semibold text-lg">{{ $obra['nombre'] }}</h3>
                                    <span
                                        class="px-2 py-1 text-xs rounded-full 
                                        @if ($obra['estado'] === 'en_progreso') bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200
                                        @elseif($obra['estado'] === 'planificada') bg-gray-100 text-gray-800 dark:bg-gray-900 dark:text-gray-200
                                        @else bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200 @endif">
                                        {{ ucfirst(str_replace('_', ' ', $obra['estado'])) }}
                                    </span>
                                </div>

                                <div class="space-y-2 text-sm text-gray-600 dark:text-gray-400">
                                    <p><strong>Código:</strong> {{ $obra['codigo'] }}</p>
                                    <p><strong>Cliente:</strong> {{ $obra['cliente'] }}</p>
                                    <p><strong>Ubicación:</strong> {{ Str::limit($obra['ubicacion'], 30) }}</p>
                                    <p><strong>Días transcurridos:</strong> {{ $obra['dias_transcurridos'] }} días</p>
                                </div>

                                <div class="mt-4 pt-4 border-t border-gray-200 dark:border-gray-700">
                                    <div class="flex justify-between items-center">
                                        <div class="text-sm">
                                            <span class="font-medium">Personal:</span>
                                            <span class="text-green-600">{{ $obra['personal_trabajando'] }}
                                                trabajando</span>
                                            @if ($obra['personal_descansando'] > 0)
                                                <span class="text-orange-600">{{ $obra['personal_descansando'] }}
                                                    descansando</span>
                                            @endif
                                        </div>
                                        <div class="text-right">
                                            <div class="text-lg font-bold">{{ $obra['personal_total'] }}</div>
                                            <div class="text-xs text-gray-500">Total</div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="text-center py-8 text-gray-500">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12 mx-auto mb-4 text-gray-400"
                            fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                        </svg>
                        <p>No hay obras activas en este momento</p>
                    </div>
                @endif
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <!-- Personal por Obra -->
            <div class="bg-white rounded-xl shadow dark:bg-gray-800 border border-gray-100 dark:border-gray-700">
                <div class="p-6 border-b border-gray-100 dark:border-gray-700">
                    <h2 class="text-xl font-semibold flex items-center">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 mr-2 text-green-500" fill="none"
                            viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                        </svg>
                        Personal Asignado
                    </h2>
                </div>
                <div class="p-6">
                    @if ($personalPorObra->count() > 0)
                        <div class="space-y-3 max-h-96 overflow-y-auto">
                            @foreach ($personalPorObra as $persona)
                                <div
                                    class="flex items-center justify-between p-3 border border-gray-200 dark:border-gray-700 rounded-lg">
                                    <div class="flex-1">
                                        <div class="font-medium">{{ $persona['nombre'] }}</div>
                                        <div class="text-sm text-gray-600 dark:text-gray-400">
                                            {{ $persona['obra'] }} ({{ $persona['obra_codigo'] }})
                                        </div>
                                        <div class="text-xs text-gray-500">
                                            Roster: {{ $persona['tipo_roster'] }}
                                        </div>
                                    </div>
                                    <div class="text-right">
                                        <span
                                            class="px-2 py-1 text-xs rounded-full
                                            @if ($persona['estado_roster'] === 'trabajando') bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200
                                            @elseif($persona['estado_roster'] === 'descansando') bg-orange-100 text-orange-800 dark:bg-orange-900 dark:text-orange-200
                                            @else bg-gray-100 text-gray-800 dark:bg-gray-900 dark:text-gray-200 @endif">
                                            {{ ucfirst($persona['estado_roster']) }}
                                        </span>
                                        @if ($persona['necesita_rotacion'])
                                            <div class="text-xs text-red-600 mt-1">¡Necesita rotación!</div>
                                        @endif
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-8 text-gray-500">
                            <p>No hay personal asignado a obras</p>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Próximas Rotaciones -->
            <div class="bg-white rounded-xl shadow dark:bg-gray-800 border border-gray-100 dark:border-gray-700">
                <div class="p-6 border-b border-gray-100 dark:border-gray-700">
                    <h2 class="text-xl font-semibold flex items-center">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 mr-2 text-orange-500" fill="none"
                            viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        Próximas Rotaciones
                    </h2>
                </div>
                <div class="p-6">
                    @if ($proximasRotaciones->count() > 0)
                        <div class="space-y-3 max-h-96 overflow-y-auto">
                            @foreach ($proximasRotaciones as $rotacion)
                                <div
                                    class="flex items-center justify-between p-3 border border-gray-200 dark:border-gray-700 rounded-lg">
                                    <div class="flex-1">
                                        <div class="font-medium">{{ $rotacion['nombre'] }}</div>
                                        <div class="text-sm text-gray-600 dark:text-gray-400">
                                            {{ $rotacion['obra'] }}
                                        </div>
                                        <div class="text-xs text-gray-500">
                                            {{ $rotacion['fecha_rotacion']->format('d/m/Y') }}
                                        </div>
                                    </div>
                                    <div class="text-right">
                                        <div
                                            class="text-sm font-medium
                                            @if ($rotacion['dias_restantes'] <= 3) text-red-600
                                            @elseif($rotacion['dias_restantes'] <= 7) text-orange-600
                                            @else text-green-600 @endif">
                                            {{ $rotacion['dias_restantes'] }} días
                                        </div>
                                        <div class="text-xs text-gray-500">
                                            {{ ucfirst($rotacion['estado_actual']) }} →
                                            {{ ucfirst($rotacion['proximo_estado']) }}
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-8 text-gray-500">
                            <p>No hay rotaciones programadas</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Estadísticas Generales -->
        <div class="bg-white rounded-xl shadow dark:bg-gray-800 border border-gray-100 dark:border-gray-700">
            <div class="p-6 border-b border-gray-100 dark:border-gray-700">
                <h2 class="text-xl font-semibold flex items-center">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 mr-2 text-blue-500" fill="none"
                        viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                    </svg>
                    Resumen General
                </h2>
            </div>
            <div class="p-6">
                <div class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-6 gap-4">
                    <div class="text-center">
                        <div class="text-2xl font-bold text-blue-600">{{ $estadisticasGenerales['obras_total'] }}</div>
                        <div class="text-sm text-gray-600">Obras Total</div>
                    </div>
                    <div class="text-center">
                        <div class="text-2xl font-bold text-green-600">{{ $estadisticasGenerales['obras_activas'] }}
                        </div>
                        <div class="text-sm text-gray-600">Obras Activas</div>
                    </div>
                    <div class="text-center">
                        <div class="text-2xl font-bold text-purple-600">{{ $estadisticasGenerales['personal_total'] }}
                        </div>
                        <div class="text-sm text-gray-600">Personal Total</div>
                    </div>
                    <div class="text-center">
                        <div class="text-2xl font-bold text-green-600">
                            {{ $estadisticasGenerales['personal_trabajando'] }}</div>
                        <div class="text-sm text-gray-600">Trabajando</div>
                    </div>
                    <div class="text-center">
                        <div class="text-2xl font-bold text-orange-600">
                            {{ $estadisticasGenerales['personal_descansando'] }}</div>
                        <div class="text-sm text-gray-600">Descansando</div>
                    </div>
                    <div class="text-center">
                        <div class="text-2xl font-bold text-red-600">
                            {{ $estadisticasGenerales['rotaciones_pendientes'] }}</div>
                        <div class="text-sm text-gray-600">Rotaciones Pendientes</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-filament-panels::page>
