<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;
use App\Services\DeepSeekService;

class Chatbot extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-chat-bubble-left-right';
    protected static ?string $navigationLabel = 'Asistente Virtual';
    protected static ?string $title = 'Asistente Virtual';
    protected static ?string $navigationGroup = 'Sistema';
    protected static ?int $navigationSort = 3;

    public $question = '';
    public $response = '';
    public $chatHistory = [];
    private DeepSeekService $deepSeekService;

    public function boot()
    {
        $this->deepSeekService = new DeepSeekService();
    }

    protected static string $view = 'filament.pages.chatbot';

    public function askChatbot()
    {
        if (!empty($this->question)) {
            try {
                $response = $this->deepSeekService->query($this->question);

                $this->chatHistory[] = [
                    'question' => $this->question,
                    'response' => $response,
                    'timestamp' => now()->format('H:i')
                ];

                $this->response = $response;
            } catch (\Exception $e) {
                $this->response = 'Error al procesar la pregunta. Por favor, intente nuevamente.';
            }

            $this->question = ''; // Limpiar el campo de pregunta
        }
    }

    public function getHelpText(): string
    {
        return "Puedes preguntarme sobre:\n" .
               "- Stocks bajos (ejemplo: '¿Qué stocks están bajos?')\n" .
               "- Valor total del inventario (ejemplo: '¿Cuál es el valor total?')\n" .
               "- Movimientos recientes (ejemplo: 'Muéstrame los últimos movimientos')\n" .
               "- Personal más activo (ejemplo: '¿Quién es el personal más activo?')\n" .
               "- Cantidad de un producto específico (ejemplo: '¿Cuánto hay de cemento?')";
    }
}
