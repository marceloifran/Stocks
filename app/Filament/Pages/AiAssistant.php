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
use Filament\Notifications\Notification;
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

    public string $query = '';
    public ?string $response = null;
    public array $chatHistory = [];
    public bool $isLoading = false;

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
        Log::info('====== INICIO DE PROCESAMIENTO DE CONSULTA ======');
        Log::info('Consulta recibida: ' . ($this->query ?: '[VACÍA]'));

        // Verificar que la consulta no esté vacía
        if (empty(trim($this->query))) {
            Log::warning('Consulta vacía detectada');
            Notification::make()
                ->title('Error')
                ->body('Por favor, ingresa una consulta.')
                ->danger()
                ->send();
            return;
        }

        try {
            $this->isLoading = true;
            Log::info('Estado de carga activado');

            // Guardar la consulta en el historial
            $this->chatHistory[] = [
                'type' => 'query',
                'content' => $this->query,
                'timestamp' => now()->format('H:i'),
            ];
            Log::info('Consulta agregada al historial');

            // Procesar la consulta
            Log::info('Iniciando procesamiento de la consulta');
            $response = $this->processQuery($this->query);
            Log::info('Respuesta obtenida: ' . substr($response, 0, 100) . (strlen($response) > 100 ? '...' : ''));

            // Verificar si la respuesta indica un error
            if (str_contains($response, 'Lo siento, ocurrió un error')) {
                throw new \Exception('Error al procesar la consulta en la API');
            }

            // Guardar la respuesta en el historial
            $this->chatHistory[] = [
                'type' => 'response',
                'content' => $response,
                'timestamp' => now()->format('H:i'),
            ];
            Log::info('Respuesta agregada al historial');

            // Guardar el historial en la sesión
            session(['chat_history' => $this->chatHistory]);
            Log::info('Historial guardado en sesión');

            $this->response = $response;
            $this->query = '';
            $this->isLoading = false;
            Log::info('Estado de carga desactivado');

            // Notificar éxito - solo si llegamos hasta aquí sin errores
            Log::info('====== FIN DE PROCESAMIENTO DE CONSULTA (ÉXITO) ======');

        } catch (\Exception $e) {
            $this->isLoading = false;
            Log::error('Error en submit: ' . $e->getMessage());
            Log::error('Trace: ' . $e->getTraceAsString());

            // Agregar mensaje de error al historial
            $this->chatHistory[] = [
                'type' => 'response',
                'content' => 'Lo siento, ocurrió un error al procesar tu consulta. Por favor, intenta de nuevo más tarde.',
                'timestamp' => now()->format('H:i'),
            ];

            // Guardar el historial en la sesión
            session(['chat_history' => $this->chatHistory]);

            Notification::make()
                ->title('Error')
                ->body('Ocurrió un error al procesar la consulta: ' . $e->getMessage())
                ->danger()
                ->send();
            Log::info('====== ERROR EN PROCESAMIENTO DE CONSULTA ======');
        }
    }

    public function clearHistory(): void
    {
        $this->chatHistory = [];
        session(['chat_history' => []]);
        $this->response = null;
    }

    private function processQuery(string $query): string
    {
        try {
            Log::info('====== INICIO DE PROCESAMIENTO DE QUERY EN API ======');

            // Obtener información del contexto para proporcionar a la IA
            Log::info('Obteniendo contexto para la consulta');
            $contextData = $this->getContextData($query);

            // Registrar el contexto para depuración
            Log::info('Contexto para la consulta: ' . $contextData);

            // Preparar el mensaje para Gemini
            Log::info('Preparando datos para la API de Gemini');
            $data = [
                'contents' => [
                    [
                        'role' => 'system',
                        'parts' => [
                            ['text' => 'Eres un asistente especializado en analizar datos de personal, asistencias, comidas y stock de una empresa. Responde de manera concisa y directa. Usa los datos proporcionados para dar respuestas precisas.']
                        ]
                    ],
                    [
                        'role' => 'user',
                        'parts' => [
                            ['text' => $query . "\n\nContexto:\n" . $contextData]
                        ]
                    ]
                ],
                'generationConfig' => [
                    'temperature' => 0.7,
                    'maxOutputTokens' => 500,
                    'topP' => 0.8,
                    'topK' => 40
                ]
            ];

            // Llamar a la API de Gemini
            $apiKey = config('services.gemini.api_key');
            Log::info('API Key de Gemini obtenida: ' . substr($apiKey, 0, 5) . '...' . substr($apiKey, -5));

            $model = config('services.gemini.model', 'gemini-1.5-flash');
            Log::info('Modelo de Gemini a utilizar: ' . $model);

            $url = "https://generativelanguage.googleapis.com/v1beta/models/{$model}:generateContent?key={$apiKey}";
            Log::info('URL de la API de Gemini: ' . $url);

            Log::info('Datos de solicitud: ' . json_encode($data, JSON_PRETTY_PRINT));

            Log::info('Enviando solicitud HTTP a la API de Gemini...');
            $response = Http::withHeaders([
                'Content-Type' => 'application/json',
            ])->post($url, $data);

            // Registrar la respuesta completa para depuración
            Log::info('Respuesta recibida con código de estado: ' . $response->status());
            Log::info('Cuerpo de la respuesta: ' . $response->body());

            if ($response->successful()) {
                Log::info('La solicitud fue exitosa (código 2xx)');

                // Extraer el texto de la respuesta de Gemini
                $responseData = $response->json();
                Log::info('Respuesta JSON de Gemini: ' . json_encode($responseData, JSON_PRETTY_PRINT));

                if (isset($responseData['candidates'][0]['content']['parts'][0]['text'])) {
                    $content = $responseData['candidates'][0]['content']['parts'][0]['text'];
                    Log::info('Texto extraído correctamente de la respuesta');
                    Log::info('====== FIN DE PROCESAMIENTO DE QUERY EN API (ÉXITO) ======');
                    return $content;
                } else {
                    Log::warning('No se encontró el texto en la respuesta de Gemini');
                    Log::warning('Estructura de la respuesta: ' . json_encode(array_keys($responseData)));
                    Log::info('====== FIN DE PROCESAMIENTO DE QUERY EN API (RESPUESTA INCOMPLETA) ======');
                    return "No se pudo obtener una respuesta clara. Por favor, intenta reformular tu pregunta.";
                }
            } else {
                Log::error('Error en la API de Gemini. Código de estado: ' . $response->status());
                Log::error('Cuerpo de la respuesta de error: ' . $response->body());
                Log::info('====== FIN DE PROCESAMIENTO DE QUERY EN API (ERROR) ======');
                return "Lo siento, ocurrió un error al procesar tu consulta. Por favor, intenta de nuevo más tarde.";
            }
        } catch (\Exception $e) {
            Log::error('Excepción al procesar la consulta: ' . $e->getMessage());
            Log::error('Trace: ' . $e->getTraceAsString());
            Log::info('====== FIN DE PROCESAMIENTO DE QUERY EN API (EXCEPCIÓN) ======');
            return "Lo siento, ocurrió un error al procesar tu consulta: " . $e->getMessage();
        }
    }

    private function getContextData(string $query): string
    {
        Log::info('Iniciando obtención de contexto para la consulta');
        $context = "";
        $queryLower = strtolower($query);

        // Identificar el tipo de consulta para proporcionar datos relevantes
        Log::info('Analizando tipo de consulta');

        if ($this->containsAny($queryLower, ['asistencia', 'asistieron', 'presente', 'faltó', 'ausente', 'vino'])) {
            Log::info('Detectada consulta de tipo: asistencia');
            $context .= $this->getAttendanceContext();
        }

        if ($this->containsAny($queryLower, ['comida', 'comieron', 'desayuno', 'almuerzo', 'merienda', 'cena'])) {
            Log::info('Detectada consulta de tipo: comida');
            $context .= $this->getMealContext();
        }

        if ($this->containsAny($queryLower, ['personal', 'empleado', 'trabajador', 'persona', 'departamento'])) {
            Log::info('Detectada consulta de tipo: personal');
            $context .= $this->getPersonnelContext();
        }

        if ($this->containsAny($queryLower, ['stock', 'epp', 'equipo', 'protección', 'entrega'])) {
            Log::info('Detectada consulta de tipo: stock');
            $context .= $this->getStockContext();
        }

        // Si no se identificó ningún contexto específico, proporcionar un resumen general
        if (empty($context)) {
            Log::info('No se detectó un tipo específico, proporcionando contexto general');
            $context = $this->getGeneralContext();
        }

        Log::info('Contexto generado con éxito (' . strlen($context) . ' caracteres)');
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
