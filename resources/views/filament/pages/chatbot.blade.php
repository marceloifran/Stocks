<x-filament-panels::page>
    <div class="space-y-4">
        <div class="p-6 bg-white dark:bg-gray-800 rounded-lg shadow-md space-y-4">
            {{-- Texto de ayuda --}}
            <div class="text-sm text-gray-600 dark:text-gray-300 bg-gray-100 dark:bg-gray-700 p-4 rounded-lg mb-4">
                <p class="font-medium mb-2">💡 Sugerencias de preguntas:</p>
                <pre class="whitespace-pre-line">{{ $this->getHelpText() }}</pre>
            </div>

            {{-- Historial del Chat --}}
            <div class="space-y-4 max-h-[500px] overflow-y-auto p-4 bg-gray-50 dark:bg-gray-900 rounded-lg">
                @foreach($chatHistory as $chat)
                    <div class="space-y-2">
                        {{-- Pregunta del usuario --}}
                        <div class="flex justify-end">
                            <div class="bg-primary-500 text-white rounded-lg p-3 max-w-[80%] shadow-sm">
                                <p class="text-sm">{{ $chat['question'] }}</p>
                                <span class="text-xs opacity-75">{{ $chat['timestamp'] }}</span>
                            </div>
                        </div>

                        {{-- Respuesta del asistente --}}
                        <div class="flex justify-start">
                            <div class="bg-white dark:bg-gray-700 text-gray-900 dark:text-white rounded-lg p-3 max-w-[80%] shadow-sm">
                                <p class="text-sm whitespace-pre-line">{{ $chat['response'] }}</p>
                                <span class="text-xs text-gray-500 dark:text-gray-400">{{ $chat['timestamp'] }}</span>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            {{-- Campo de entrada y botón --}}
            <div class="flex space-x-2 mt-4">
                <div class="flex-1">
                    <input
                        type="text"
                        wire:model="question"
                        wire:keydown.enter="askChatbot"
                        class="w-full px-4 py-2 rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:border-primary-500 focus:ring-2 focus:ring-primary-500 dark:focus:border-primary-500 dark:focus:ring-primary-500"
                        placeholder="Escribe tu pregunta aquí..."
                    >
                </div>
                <button
                    wire:click="askChatbot"
                    class="px-6 py-2 rounded-lg bg-primary-600 hover:bg-primary-500 text-white font-medium transition-colors duration-200 flex items-center space-x-2 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800"
                >
                    <span>Enviar</span>
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/>
                    </svg>
                </button>
            </div>
        </div>
    </div>
</x-filament-panels::page>
