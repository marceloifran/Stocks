<x-filament-panels::page>
    <div class="space-y-6">
        <div class="p-4 bg-white rounded-xl shadow dark:bg-gray-800">
            <h2 class="text-xl font-bold mb-4">Asistente IA</h2>
            <p class="mb-4">Este asistente te permite hacer consultas en lenguaje natural sobre el sistema. Puedes
                preguntar sobre asistencias, comidas, personal y más.</p>
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
                                <p class="whitespace-pre-wrap">
                                    @php
                                        // Eliminar cualquier bloque de código SQL de la respuesta
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
        <div class="p-4 bg-white rounded-xl shadow dark:bg-gray-800">
            <form wire:submit.prevent="submit">
                <div class="mb-4">
                    <label for="query"
                        class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Consulta</label>
                    <textarea id="query" wire:model="query" rows="3"
                        class="w-full rounded-lg border-gray-300 dark:border-gray-700 dark:bg-gray-800 dark:text-white shadow-sm"
                        placeholder="Escribe tu consulta en lenguaje natural, por ejemplo: '¿Cuántas personas asistieron ayer?' o '¿Quién faltó esta semana?'"></textarea>
                </div>

                <div class="flex justify-between mt-6">
                    <x-filament::button wire:click="clearHistory" type="button" color="danger" icon="heroicon-o-trash"
                        class="px-4 py-2">
                        Limpiar historial
                    </x-filament::button>

                    <x-filament::button type="submit" wire:loading.attr="disabled" color="primary"
                        icon="heroicon-o-paper-airplane" class="px-4 py-2">
                        <span wire:loading.remove>Enviar</span>
                        <span wire:loading>Procesando...</span>
                    </x-filament::button>
                </div>
            </form>
        </div>
    </div>

    <script>
        document.addEventListener('livewire:initialized', function() {
            const chatContainer = document.getElementById('chat-container');

            // Scroll to bottom on load
            if (chatContainer) {
                chatContainer.scrollTop = chatContainer.scrollHeight;
            }

            // Scroll to bottom when content changes
            Livewire.hook('message.processed', (message, component) => {
                if (chatContainer) {
                    chatContainer.scrollTop = chatContainer.scrollHeight;
                }
            });
        });
    </script>
</x-filament-panels::page>
