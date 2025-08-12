<x-filament-panels::page>
    <div class="space-y-6">
        <div class="p-6 bg-white rounded-xl shadow dark:bg-gray-800 border border-gray-100 dark:border-gray-700">
            <h2 class="text-2xl font-bold mb-4 flex items-center">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 mr-2 text-primary-500" fill="none"
                    viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z" />
                </svg>
                Asistente IA
            </h2>
            <p class="mb-4 text-gray-600 dark:text-gray-300">Este asistente te permite hacer consultas en lenguaje
                natural sobre el sistema. Puedes preguntar sobre asistencias, comidas, personal y más.</p>
        </div>

        <!-- Chat history -->
        <div class="p-4 bg-white rounded-xl shadow dark:bg-gray-800 min-h-[400px] max-h-[500px] overflow-y-auto flex flex-col space-y-4 border border-gray-100 dark:border-gray-700 scroll-smooth"
            id="chat-container">
            @if (empty($chatHistory))
                <div class="flex flex-col items-center justify-center h-full">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12 text-gray-400 mb-2" fill="none"
                        viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" />
                    </svg>
                    <p class="text-gray-500 dark:text-gray-400">No hay mensajes. Comienza una conversación.</p>
                </div>
            @else
                @foreach ($chatHistory as $message)
                    @if ($message['type'] === 'query')
                        <div class="flex justify-end">
                            <div class="bg-primary-500 text-white rounded-lg py-3 px-4 max-w-[80%] shadow-sm">
                                <p class="whitespace-pre-wrap">{{ $message['content'] }}</p>
                                <p class="text-xs text-white/70 text-right mt-1">{{ $message['timestamp'] }}</p>
                            </div>
                        </div>
                    @else
                        <div class="flex justify-start">
                            <div class="bg-gray-100 dark:bg-gray-700 rounded-lg py-3 px-4 max-w-[80%] shadow-sm">
                                <p class="whitespace-pre-wrap">
                                    @php
                                        $content = preg_replace('/```sql\s*(.*?)\s*```/s', '', $message['content']);
                                        $content = preg_replace('/`(SELECT.*?)`/is', '', $content);
                                        $content = preg_replace('/"sql\s*(.*?)"/s', '', $content);
                                        $content = preg_replace('/```(.*?)```/s', '', $content);
                                        echo $content;
                                    @endphp
                                </p>
                                <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">{{ $message['timestamp'] }}</p>
                            </div>
                        </div>
                    @endif
                @endforeach
            @endif
        </div>

        <!-- Input form -->
        <div class="p-6 bg-white rounded-xl shadow dark:bg-gray-800 border border-gray-100 dark:border-gray-700">
            <form wire:submit.prevent="submit">
                <div class="mb-4">
                    <label for="query" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">¿En
                        qué puedo ayudarte?</label>
                    <div class="relative">
                        <textarea id="query" wire:model="query" rows="3"
                            class="w-full rounded-lg border-gray-300 dark:border-gray-700 dark:bg-gray-800 dark:text-white shadow-sm focus:border-primary-500 focus:ring focus:ring-primary-500 focus:ring-opacity-50"
                            placeholder="Escribe tu consulta en lenguaje natural, por ejemplo: '¿Cuántas personas asistieron ayer?' o '¿Quién faltó esta semana?'"
                            {{ $isLoading ? 'disabled' : '' }}></textarea>
                        @if ($isLoading)
                            <div
                                class="absolute inset-0 bg-gray-100/50 dark:bg-gray-700/50 flex items-center justify-center rounded-lg">
                                <svg class="animate-spin h-5 w-5 text-primary-500" xmlns="http://www.w3.org/2000/svg"
                                    fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10"
                                        stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor"
                                        d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                                    </path>
                                </svg>
                            </div>
                        @endif
                    </div>
                </div>

                <div class="flex justify-between mt-6">
                    <x-filament::button wire:click="clearHistory" type="button" color="danger"
                        class="px-4 py-2 flex items-center gap-2" {{ $isLoading ? 'disabled' : '' }}>
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24"
                            stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                        </svg>
                        Limpiar historial
                    </x-filament::button>

                    <x-filament::button type="submit" wire:loading.attr="disabled" color="primary"
                        class="px-4 py-2 flex items-center gap-2" {{ $isLoading ? 'disabled' : '' }}>
                        <span wire:loading.remove>
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24"
                                stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8" />
                            </svg>
                            Enviar
                        </span>
                        <span wire:loading>
                            <svg class="animate-spin h-4 w-4 mr-1" xmlns="http://www.w3.org/2000/svg" fill="none"
                                viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor"
                                    stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor"
                                    d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                                </path>
                            </svg>
                            Procesando...
                        </span>
                    </x-filament::button>
                </div>
            </form>
        </div>
    </div>

    <script>
        document.addEventListener('livewire:initialized', function() {
            const chatContainer = document.getElementById('chat-container');

            if (chatContainer) {
                chatContainer.scrollTop = chatContainer.scrollHeight;
            }

            Livewire.hook('message.processed', (message, component) => {
                if (chatContainer) {
                    setTimeout(() => {
                        chatContainer.scrollTop = chatContainer.scrollHeight;
                    }, 100);
                }
            });
        });
    </script>
</x-filament-panels::page>
