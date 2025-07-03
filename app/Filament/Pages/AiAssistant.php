<?php

namespace App\Filament\Pages;

use App\Models\asistencia;
use App\Models\Comida;
use App\Models\personal;
use App\Models\StockMovement;
use Carbon\Carbon;
use Filament\Forms\Components\Actions\Action;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Pages\Page;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class AiAssistant extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-chat-bubble-left-right';
    protected static ?string $navigationLabel = 'Asistente IA';
    protected static ?string $title = 'Asistente IA';
    protected static ?string $slug = 'ai-assistant';
    protected static ?string $navigationGroup = 'Administrative';
    protected static ?int $navigationSort = 5;

    protected static string $view = 'filament.pages.ai-assistant';

    public ?string $query = null;
    public ?string $response = null;
    public array $chatHistory = [];
    public bool $isLoading = false;
    public array $exampleQuestions = [
        '¿Cuántas personas asistieron ayer?',
        '¿Quién faltó esta semana?',
        '¿Cuántas comidas se sirvieron hoy?',
        '¿Cuántas personas hay en el departamento de Producción?',
        '¿Cuántos EPP se entregaron este mes?'
    ];

    public function mount(): void
    {
        $this->chatHistory = session('chat_history', []);
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Textarea::make('query')
                    ->label('Consulta')
                    ->placeholder('Escribe tu consulta en lenguaje natural, por ejemplo: "¿Cuántas personas asistieron ayer?" o "¿Quién faltó esta semana?"')
                    ->rows(3)
                    ->columnSpanFull(),
            ])
            ->statePath('query');
    }

    public function submit(): void
    {
        // Verificar que la consulta no esté vacía
        if (empty($this->query)) {
            return;
        }

        $this->isLoading = true;

        // Guardar la consulta en el historial
        $this->chatHistory[] = [
            'type' => 'query',
            'content' => $this->query,
            'timestamp' => now()->format('H:i'),
        ];

        // Procesar la consulta
        $response = $this->processQuery($this->query);

        // Guardar la respuesta en el historial
        $this->chatHistory[] = [
            'type' => 'response',
            'content' => $response,
            'timestamp' => now()->format('H:i'),
        ];

        // Guardar el historial en la sesión
        session(['chat_history' => $this->chatHistory]);

        $this->response = $response;
        $this->query = null;
        $this->isLoading = false;
    }

    public function clearHistory(): void
    {
        $this->chatHistory = [];
        session(['chat_history' => []]);
        $this->response = null;
    }

    public function useExampleQuestion(string $question): void
    {
        $this->query = $question;
    }

    private function processQuery(string $query): string
    {
        try {
            // Obtener información del contexto para proporcionar a la IA
            $contextData = $this->getContextData($query);

            // Preparar el mensaje para OpenAI
            $messages = [
                [
                    'role' => 'system',
                    'content' => 'Eres un asistente especializado en analizar datos de personal, asistencias, comidas y stock de una empresa. Responde de manera concisa y directa. Usa los datos proporcionados para dar respuestas precisas.'
                ],
                [
                    'role' => 'user',
                    'content' => $query . "\n\nContexto:\n" . $contextData
                ]
            ];

            // Llamar a la API de OpenAI
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . config('services.openai.api_key'),
                'Content-Type' => 'application/json',
            ])->post('https://api.openai.com/v1/chat/completions', [
                'model' => config('services.openai.model', 'gpt-4o'),
                'messages' => $messages,
                'temperature' => 0.7,
                'max_tokens' => 500,
            ]);

            // Registrar la respuesta completa para depuración
            \Illuminate\Support\Facades\Log::info('Respuesta de OpenAI: ' . json_encode($response->json()));

            if ($response->successful()) {
                return $response->json('choices.0.message.content');
            } else {
                \Illuminate\Support\Facades\Log::error('Error en la API de OpenAI: ' . $response->body());
                return "Lo siento, ocurrió un error al procesar tu consulta. Por favor, intenta de nuevo más tarde.";
            }
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Error al procesar la consulta: ' . $e->getMessage());
            return "Lo siento, ocurrió un error al procesar tu consulta: " . $e->getMessage();
        }
    }

    private function getContextData(string $query): string
    {
        $context = "";
        $queryLower = strtolower($query);

        // Identificar el tipo de consulta para proporcionar datos relevantes
        if ($this->containsAny($queryLower, ['asistencia', 'asistieron', 'presente', 'faltó', 'ausente', 'vino'])) {
            $context .= $this->getAttendanceContext();
        }

        if ($this->containsAny($queryLower, ['comida', 'comieron', 'desayuno', 'almuerzo', 'merienda', 'cena'])) {
            $context .= $this->getMealContext();
        }

        if ($this->containsAny($queryLower, ['personal', 'empleado', 'trabajador', 'persona', 'departamento'])) {
            $context .= $this->getPersonnelContext();
        }

        if ($this->containsAny($queryLower, ['stock', 'epp', 'equipo', 'protección', 'entrega'])) {
            $context .= $this->getStockContext();
        }

        // Si no se identificó ningún contexto específico, proporcionar un resumen general
        if (empty($context)) {
            $context = $this->getGeneralContext();
        }

        return $context;
    }

    private function containsAny(string $haystack, array $needles): bool
    {
        foreach ($needles as $needle) {
            if (str_contains($haystack, $needle)) {
                return true;
            }
        }
        return false;
    }

    private function getAttendanceContext(): string
    {
        $today = Carbon::today();
        $yesterday = Carbon::yesterday();

        $todayAttendance = asistencia::whereDate('fecha', $today)
            ->where('estado', 'entrada')
            ->count();

        $yesterdayAttendance = asistencia::whereDate('fecha', $yesterday)
            ->where('estado', 'entrada')
            ->count();

        $thisWeekAttendance = asistencia::whereBetween('fecha', [
            $today->copy()->startOfWeek()->format('Y-m-d'),
            $today->format('Y-m-d')
        ])
            ->where('estado', 'entrada')
            ->count();

        $totalPersons = personal::count();

        return "Datos de asistencia:\n" .
            "- Total de personal: $totalPersons\n" .
            "- Asistencias hoy: $todayAttendance\n" .
            "- Asistencias ayer: $yesterdayAttendance\n" .
            "- Asistencias esta semana: $thisWeekAttendance\n";
    }

    private function getMealContext(): string
    {
        $today = Carbon::today();

        $todayMeals = Comida::whereDate('fecha', $today)->count();

        $mealsByType = Comida::select('tipo_comida', DB::raw('count(*) as total'))
            ->whereDate('fecha', $today)
            ->groupBy('tipo_comida')
            ->get()
            ->pluck('total', 'tipo_comida')
            ->toArray();

        $context = "Datos de comidas de hoy:\n" .
            "- Total de comidas: $todayMeals\n";

        if (!empty($mealsByType)) {
            $context .= "- Por tipo: ";
            $parts = [];
            foreach ($mealsByType as $type => $count) {
                $parts[] = "$type: $count";
            }
            $context .= implode(", ", $parts) . "\n";
        }

        return $context;
    }

    private function getPersonnelContext(): string
    {
        $totalPersons = personal::count();

        $personnelByDept = personal::select('departamento', DB::raw('count(*) as total'))
            ->whereNotNull('departamento')
            ->groupBy('departamento')
            ->get()
            ->pluck('total', 'departamento')
            ->toArray();

        $context = "Datos de personal:\n" .
            "- Total de personal: $totalPersons\n";

        if (!empty($personnelByDept)) {
            $context .= "- Por departamento: ";
            $parts = [];
            foreach ($personnelByDept as $dept => $count) {
                $parts[] = "$dept: $count";
            }
            $context .= implode(", ", $parts) . "\n";
        }

        return $context;
    }

    private function getStockContext(): string
    {
        $today = Carbon::today();
        $thisMonth = Carbon::today()->startOfMonth();

        $todayMovements = StockMovement::whereDate('fecha_movimiento', $today)->count();
        $monthMovements = StockMovement::whereBetween('fecha_movimiento', [
            $thisMonth->format('Y-m-d'),
            $today->format('Y-m-d')
        ])
            ->count();

        return "Datos de stock y EPP:\n" .
            "- Entregas hoy: $todayMovements\n" .
            "- Entregas este mes: $monthMovements\n";
    }

    private function getGeneralContext(): string
    {
        $today = Carbon::today();

        $totalPersons = personal::count();
        $todayAttendance = asistencia::whereDate('fecha', $today)->where('estado', 'entrada')->count();
        $attendancePercentage = $totalPersons > 0 ? round(($todayAttendance / $totalPersons) * 100, 1) : 0;

        $todayMeals = Comida::whereDate('fecha', $today)->count();
        $todayEPP = StockMovement::whereDate('fecha_movimiento', $today)->count();

        return "Resumen general:\n" .
            "- Total de personal: $totalPersons\n" .
            "- Asistencia hoy: $todayAttendance personas ($attendancePercentage%)\n" .
            "- Comidas servidas hoy: $todayMeals\n" .
            "- Entregas de EPP hoy: $todayEPP\n";
    }
}
