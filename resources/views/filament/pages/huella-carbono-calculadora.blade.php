<x-filament-panels::page>
    <div class="space-y-6">
        <div class="p-6 bg-white rounded-xl shadow dark:bg-gray-800 border border-gray-100 dark:border-gray-700">
            <h2 class="text-2xl font-bold mb-4 flex items-center">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 mr-2 text-primary-500" fill="none"
                    viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                </svg>
                Calculadora de Huella de Carbono
            </h2>
            <p class="mb-4 text-gray-600 dark:text-gray-300">
                Calcule las emisiones de CO2 equivalente para diferentes fuentes de emisión en sus obras.
            </p>
        </div>

        <div class="p-6 bg-white rounded-xl shadow dark:bg-gray-800 border border-gray-100 dark:border-gray-700">
            {{ $this->form }}

            <div class="flex justify-end mt-6">
                <x-filament::button wire:click="guardar" type="button" color="primary" class="px-4 py-2">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-1" fill="none" viewBox="0 0 24 24"
                        stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4" />
                    </svg>
                    Guardar Registro
                </x-filament::button>
            </div>
        </div>

        <div class="p-6 bg-white rounded-xl shadow dark:bg-gray-800 border border-gray-100 dark:border-gray-700">
            <h3 class="text-lg font-semibold mb-4">Información sobre Huella de Carbono</h3>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <h4 class="font-medium mb-2">¿Qué es la huella de carbono?</h4>
                    <p class="text-gray-600 dark:text-gray-400">
                        La huella de carbono es la totalidad de gases de efecto invernadero (GEI) emitidos por efecto
                        directo o indirecto de un individuo, organización, evento o producto.
                    </p>
                </div>

                <div>
                    <h4 class="font-medium mb-2">¿Cómo se calcula?</h4>
                    <p class="text-gray-600 dark:text-gray-400">
                        Se calcula multiplicando los datos de actividad (consumo de combustible, electricidad,
                        generación de residuos, etc.) por factores de emisión específicos para cada fuente.
                    </p>
                </div>

                <div>
                    <h4 class="font-medium mb-2">¿Por qué es importante medirla?</h4>
                    <p class="text-gray-600 dark:text-gray-400">
                        Medir la huella de carbono permite identificar oportunidades de reducción de emisiones, mejorar
                        la eficiencia energética y cumplir con regulaciones ambientales.
                    </p>
                </div>

                <div>
                    <h4 class="font-medium mb-2">Normativa aplicable</h4>
                    <p class="text-gray-600 dark:text-gray-400">
                        En Argentina, la Ley N° 24.585 de Protección Ambiental para la Actividad Minera exige que las
                        empresas presenten una evaluación de impacto ambiental que incluye la gestión de emisiones.
                    </p>
                </div>
            </div>
        </div>
    </div>
</x-filament-panels::page>
