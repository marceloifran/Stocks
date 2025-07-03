<x-filament-panels::page>
    <div class="space-y-6">
        <div class="p-4 bg-white rounded-xl shadow dark:bg-gray-800">
            <h2 class="text-xl font-bold mb-4">Asistente IA</h2>
            <p class="mb-4">Este asistente te permite hacer consultas en lenguaje natural sobre el sistema. Puedes
                preguntar sobre asistencias, comidas, personal y más.</p>

            <div class="mt-2">
                <p class="font-medium mb-2">Ejemplos de preguntas:</p>
                <div class="flex flex-wrap gap-2">
                    @foreach ($exampleQuestions as $question)
                        <button wire:click="useExampleQuestion('{{ $question }}')"
                            class="text-sm bg-gray-100 hover:bg-gray-200 dark:bg-gray-700 dark:hover:bg-gray-600 rounded-full py-1 px-3 transition-colors">
                            {{ $question }}
                        </button>
                    @endforeach
                </div>
            </div>
        </div>

        <!-- Chat history -->
        <div class="p-4 bg-white rounded-xl shadow dark:bg-gray-800 min-h-[300px] max-h-[500px] overflow-y-auto flex flex-col space-y-4"
            id="chat-container">
            @if (empty($chatHistory))
                <div class="flex items-center justify-center h-full">
                    <p class="text-gray-500 dark:text-gray-400">No hay mensajes. Comienza una conversación.</p>
                </div>
            @else
                @foreach ($chatHistory as $message)
                    @if ($message['type'] === 'query')
                        <div class="flex justify-end">
                            <div class="bg-primary-500 text-white rounded-lg py-2 px-4 max-w-[80%]">
                                <p class="whitespace-pre-wrap">{{ $message['content'] }}</p>
                                <p class="text-xs text-white/70 text-right mt-1">{{ $message['timestamp'] }}</p>
                            </div>
                        </div>
                    @else
                        <div class="flex justify-start">
                            <div class="bg-gray-100 dark:bg-gray-700 rounded-lg py-2 px-4 max-w-[80%]">
                                <p class="whitespace-pre-wrap">{{ $message['content'] }}</p>
                                <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">{{ $message['timestamp'] }}</p>
                            </div>
                        </div>
                    @endif
                @endforeach
            @endif
        </div>

        <!-- Input form -->
        <div class="p-4 bg-white rounded-xl shadow dark:bg-gray-800">
            {{ $this->form }}

            <div class="flex justify-between mt-4">
                <x-filament::button wire:click="clearHistory" color="danger" icon="heroicon-o-trash">
                    Limpiar historial
                </x-filament::button>

                <x-filament::button wire:click="submit" wire:loading.attr="disabled" color="primary"
                    icon="heroicon-o-paper-airplane">
                    Enviar
                </x-filament::button>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('livewire:initialized', function() {
            const chatContainer = document.getElementById('chat-container');

            // Scroll to bottom on load
            chatContainer.scrollTop = chatContainer.scrollHeight;

            // Scroll to bottom when content changes
            Livewire.hook('message.processed', (message, component) => {
                chatContainer.scrollTop = chatContainer.scrollHeight;
            });
        });
    </script>
</x-filament-panels::page>
