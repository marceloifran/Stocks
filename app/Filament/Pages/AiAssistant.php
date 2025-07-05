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
        // Este método ya no se usa porque estamos usando un textarea directamente en la vista
        return $form->schema([]);
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

            // Obtener contexto para la consulta
            Log::info('Obteniendo contexto para la consulta');
            $contextData = $this->getContextData($query);
            Log::info('Contexto para la consulta: ' . $contextData);

            // Preparar el mensaje para Gemini
            Log::info('Preparando datos para la API de Gemini');

            // Instrucciones del sistema como parte del mensaje del usuario
            $systemInstruction = 'Eres un asistente amigable que trabaja en una empresa. Tu nombre es Asistente IA. ' .
                'Responde de manera conversacional y natural, como si fueras una persona real. ' .
                'Usa un tono amable y profesional. Evita mencionar que eres una IA o que estás procesando datos. ' .
                'No menciones consultas SQL ni detalles técnicos en tus respuestas. ' .
                'Usa los datos proporcionados para dar respuestas precisas y concisas.';

            $data = [
                'contents' => [
                    [
                        'role' => 'user',
                        'parts' => [
                            ['text' => $systemInstruction . "\n\n" .
                                "Pregunta del usuario: " . $query . "\n\n" .
                                "Contexto con información relevante:\n" . $contextData]
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
                    return "No pude encontrar la información que buscas. ¿Podrías reformular tu pregunta?";
                }
            } else {
                Log::error('Error en la API de Gemini. Código de estado: ' . $response->status());
                Log::error('Cuerpo de la respuesta de error: ' . $response->body());
                Log::info('====== FIN DE PROCESAMIENTO DE QUERY EN API (ERROR) ======');
                return "Disculpa, en este momento no puedo procesar tu consulta. Por favor, intenta de nuevo más tarde.";
            }
        } catch (\Exception $e) {
            Log::error('Excepción al procesar la consulta: ' . $e->getMessage());
            Log::error('Trace: ' . $e->getTraceAsString());
            Log::info('====== FIN DE PROCESAMIENTO DE QUERY EN API (EXCEPCIÓN) ======');
            return "Disculpa, ha ocurrido un problema al procesar tu consulta. Por favor, intenta de nuevo.";
        }
    }

    private function getContextData(string $query): string
    {
        try {
            $today = Carbon::today();
            $query = strtolower($query);

            // Detectar el tipo de consulta para proporcionar contexto relevante
            if (
                str_contains($query, 'asistencia') ||
                str_contains($query, 'presente') ||
                str_contains($query, 'ausente') ||
                str_contains($query, 'faltó') ||
                str_contains($query, 'asistieron')
            ) {

                Log::info('Detectada consulta de tipo: asistencia');

                // Obtener datos de asistencia
                $totalPersons = personal::count();
                $todayAttendance = asistencia::whereDate('fecha', $today)
                    ->where('estado', 'entrada')
                    ->count();
                $attendancePercentage = $totalPersons > 0 ? round(($todayAttendance / $totalPersons) * 100) : 0;

                // Si la consulta menciona fechas específicas, ajustar el contexto
                if ($this->containsDateReference($query)) {
                    $dateRange = $this->extractDateRange($query);
                    if ($dateRange) {
                        $startDate = $dateRange['start'];
                        $endDate = $dateRange['end'];

                        $attendanceInRange = asistencia::whereBetween('fecha', [$startDate, $endDate])
                            ->where('estado', 'entrada')
                            ->count();

                        return "Datos de asistencia para el período especificado:\n" .
                            "- Total de personal: {$totalPersons}\n" .
                            "- Asistencias registradas en el período: {$attendanceInRange}\n";
                    }
                }

                return "Datos de asistencia:\n" .
                    "- Total de personal: {$totalPersons}\n" .
                    "- Asistencia hoy: {$todayAttendance} personas ({$attendancePercentage}%)\n";
            }

            // Consulta sobre comidas
            elseif (
                str_contains($query, 'comida') ||
                str_contains($query, 'comió') ||
                str_contains($query, 'comieron') ||
                str_contains($query, 'almuerzo') ||
                str_contains($query, 'desayuno') ||
                str_contains($query, 'cena')
            ) {

                Log::info('Detectada consulta de tipo: comida');

                // Obtener datos de comidas
                $todayMeals = Comida::whereDate('fecha', $today)->count();
                $breakfastCount = Comida::whereDate('fecha', $today)->where('tipo_comida', 'desayuno')->count();
                $lunchCount = Comida::whereDate('fecha', $today)->where('tipo_comida', 'almuerzo')->count();
                $snackCount = Comida::whereDate('fecha', $today)->where('tipo_comida', 'merienda')->count();
                $dinnerCount = Comida::whereDate('fecha', $today)->where('tipo_comida', 'cena')->count();

                return "Datos de comidas:\n" .
                    "- Comidas servidas hoy: {$todayMeals}\n" .
                    "- Desayunos: {$breakfastCount}\n" .
                    "- Almuerzos: {$lunchCount}\n" .
                    "- Meriendas: {$snackCount}\n" .
                    "- Cenas: {$dinnerCount}\n";
            }

            // Consulta sobre personal
            elseif (
                str_contains($query, 'personal') ||
                str_contains($query, 'empleado') ||
                str_contains($query, 'trabajador') ||
                str_contains($query, 'persona')
            ) {

                Log::info('Detectada consulta de tipo: personal');

                // Si la consulta menciona un departamento específico
                $departamento = $this->extractDepartment($query);
                if ($departamento) {
                    $deptCount = personal::where('departamento', $departamento)->count();
                    return "Datos de personal por departamento:\n" .
                        "- Total de personal en {$departamento}: {$deptCount}\n";
                }

                // Si la consulta menciona un nombre específico
                $nombre = $this->extractPersonName($query);
                if ($nombre) {
                    $personInfo = personal::where('nombre', 'like', "%{$nombre}%")->first();
                    if ($personInfo) {
                        return "Datos de la persona:\n" .
                            "- Nombre: {$personInfo->nombre}\n" .
                            "- Departamento: {$personInfo->departamento}\n" .
                            "- ID: {$personInfo->nro_identificacion}\n";
                    }
                }

                // Información general de personal
                $totalPersons = personal::count();
                $deptCount = personal::select('departamento')
                    ->whereNotNull('departamento')
                    ->distinct()
                    ->count();

                return "Datos de personal:\n" .
                    "- Total de personal: {$totalPersons}\n";
            }

            // Si no se detecta un tipo específico, proporcionar un contexto general
            else {
                Log::info('No se detectó un tipo específico, proporcionando contexto general');

                $totalPersons = personal::count();
                $todayAttendance = asistencia::whereDate('fecha', $today)
                    ->where('estado', 'entrada')
                    ->count();
                $attendancePercentage = $totalPersons > 0 ? round(($todayAttendance / $totalPersons) * 100) : 0;
                $todayMeals = Comida::whereDate('fecha', $today)->count();
                $todayStock = StockMovement::whereDate('fecha_movimiento', $today)->count();

                return "Resumen general:\n" .
                    "- Total de personal: {$totalPersons}\n" .
                    "- Asistencia hoy: {$todayAttendance} personas ({$attendancePercentage}%)\n" .
                    "- Comidas servidas hoy: {$todayMeals}\n" .
                    "- Entregas de EPP hoy: {$todayStock}\n";
            }
        } catch (\Exception $e) {
            Log::error('Error al obtener contexto: ' . $e->getMessage());
            return "Error al obtener datos de contexto.";
        }

        Log::info('Contexto generado con éxito');
        return "No se pudo determinar un contexto específico.";
    }

    private function containsDateReference(string $query): bool
    {
        $dateKeywords = ['ayer', 'hoy', 'anteayer', 'semana', 'mes', 'lunes', 'martes', 'miércoles', 'jueves', 'viernes', 'sábado', 'domingo', 'enero', 'febrero', 'marzo', 'abril', 'mayo', 'junio', 'julio', 'agosto', 'septiembre', 'octubre', 'noviembre', 'diciembre'];

        foreach ($dateKeywords as $keyword) {
            if (str_contains($query, $keyword)) {
                return true;
            }
        }

        // Buscar patrones de fecha (dd/mm/yyyy, dd-mm-yyyy, etc.)
        if (preg_match('/\d{1,2}[-\/]\d{1,2}([-\/]\d{2,4})?/', $query)) {
            return true;
        }

        return false;
    }

    private function extractDateRange(string $query): ?array
    {
        $today = Carbon::today();

        if (str_contains($query, 'ayer')) {
            return [
                'start' => Carbon::yesterday()->startOfDay(),
                'end' => Carbon::yesterday()->endOfDay(),
            ];
        }

        if (str_contains($query, 'hoy')) {
            return [
                'start' => $today->copy()->startOfDay(),
                'end' => $today->copy()->endOfDay(),
            ];
        }

        if (str_contains($query, 'esta semana')) {
            return [
                'start' => $today->copy()->startOfWeek(),
                'end' => $today->copy()->endOfWeek(),
            ];
        }

        if (str_contains($query, 'este mes')) {
            return [
                'start' => $today->copy()->startOfMonth(),
                'end' => $today->copy()->endOfMonth(),
            ];
        }

        // Si no se detecta un rango específico
        return null;
    }

    private function extractDepartment(string $query): ?string
    {
        $departments = [
            'Administración',
            'Producción',
            'Logística',
            'Ventas',
            'Recursos Humanos',
            'TI',
            'Otro'
        ];

        foreach ($departments as $dept) {
            if (str_contains(strtolower($query), strtolower($dept))) {
                return $dept;
            }
        }

        return null;
    }

    private function extractPersonName(string $query): ?string
    {
        // Buscar patrones como "información de [nombre]" o "datos de [nombre]"
        if (preg_match('/(?:información|datos|info) (?:de|sobre|del?) ([a-zA-Z\s]+)/', $query, $matches)) {
            return trim($matches[1]);
        }

        return null;
    }
}
