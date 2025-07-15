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

            // Verificar longitud de la consulta
            if (strlen($this->query) > 500) {
                Log::warning('Consulta demasiado larga: ' . strlen($this->query) . ' caracteres');
                throw new \Exception('La consulta es demasiado larga. Por favor, sé más conciso.');
            }

            // Procesar la consulta
            Log::info('Iniciando procesamiento de la consulta');
            $response = $this->processQuery($this->query);
            Log::info('Respuesta obtenida: ' . substr($response, 0, 100) . (strlen($response) > 100 ? '...' : ''));

            // Verificar si la respuesta indica un error
            if (str_contains($response, 'Lo siento, ocurrió un error') || empty(trim($response))) {
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

            // Validar la consulta
            if (empty(trim($query))) {
                throw new \Exception('La consulta está vacía');
            }

            // Obtener contexto para la consulta
            Log::info('Obteniendo contexto para la consulta');
            $contextData = $this->getContextData($query);

            if (empty($contextData) || $contextData === "Error al obtener datos de contexto.") {
                Log::warning('No se pudo obtener contexto válido');
                $contextData = "No hay datos de contexto disponibles.";
            }

            Log::info('Contexto para la consulta: ' . $contextData);

            // Preparar el mensaje para Gemini
            Log::info('Preparando datos para la API de Gemini');

            // Instrucciones del sistema como parte del mensaje del usuario
            $systemInstruction = 'Eres un asistente amigable que trabaja en una empresa de gestión de personal y stock. Tu nombre es Asistente IA. ' .
                'Responde de manera conversacional y natural, como si fueras una persona real que conoce bien el sistema. ' .
                'Usa un tono amable y profesional. Evita mencionar que eres una IA o que estás procesando datos. ' .
                'No menciones consultas SQL ni detalles técnicos en tus respuestas. ' .
                'Usa los datos proporcionados para dar respuestas precisas, útiles y en español. ' .
                'Si no tienes información suficiente, sugiere preguntas relacionadas que sí puedes responder. ' .
                'Siempre proporciona contexto útil y actionable en tus respuestas.';

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

            if (empty($apiKey)) {
                throw new \Exception('API Key de Gemini no configurada');
            }

            Log::info('API Key de Gemini obtenida: ' . substr($apiKey, 0, 5) . '...' . substr($apiKey, -5));

            $model = config('services.gemini.model', 'gemini-1.5-flash');
            Log::info('Modelo de Gemini a utilizar: ' . $model);

            $url = "https://generativelanguage.googleapis.com/v1beta/models/{$model}:generateContent?key={$apiKey}";
            Log::info('URL de la API de Gemini: ' . $url);

            Log::info('Datos de solicitud: ' . json_encode($data, JSON_PRETTY_PRINT));

            // Establecer timeout para evitar esperas largas
            Log::info('Enviando solicitud HTTP a la API de Gemini...');
            $response = Http::timeout(15)->withHeaders([
                'Content-Type' => 'application/json',
            ])->post($url, $data);

            // Registrar la respuesta completa para depuración
            Log::info('Respuesta recibida con código de estado: ' . $response->status());
            Log::info('Cuerpo de la respuesta: ' . $response->body());

            if ($response->successful()) {
                Log::info('La solicitud fue exitosa (código 2xx)');

                // Extraer el texto de la respuesta de Gemini
                $responseData = $response->json();

                if (empty($responseData)) {
                    throw new \Exception('Respuesta vacía de la API');
                }

                Log::info('Respuesta JSON de Gemini: ' . json_encode($responseData, JSON_PRETTY_PRINT));

                if (isset($responseData['candidates'][0]['content']['parts'][0]['text'])) {
                    $content = $responseData['candidates'][0]['content']['parts'][0]['text'];

                    if (empty(trim($content))) {
                        throw new \Exception('La API devolvió una respuesta vacía');
                    }

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
                $errorCode = $response->status();
                $errorBody = $response->body();
                Log::error('Error en la API de Gemini. Código de estado: ' . $errorCode);
                Log::error('Cuerpo de la respuesta de error: ' . $errorBody);
                Log::info('====== FIN DE PROCESAMIENTO DE QUERY EN API (ERROR) ======');

                // Mensajes personalizados según el código de error
                if ($errorCode == 429) {
                    return "Disculpa, estamos recibiendo demasiadas consultas en este momento. Por favor, intenta de nuevo en unos minutos.";
                } elseif ($errorCode >= 500) {
                    return "Disculpa, el servicio de respuestas está experimentando problemas. Por favor, intenta de nuevo más tarde.";
                } else {
                    return "Disculpa, en este momento no puedo procesar tu consulta. Por favor, intenta de nuevo más tarde.";
                }
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
            Log::info('Obteniendo contexto para consulta: ' . $query);

            // Detectar el tipo de consulta para proporcionar contexto relevante
            if (
                str_contains($query, 'asistencia') ||
                str_contains($query, 'presente') ||
                str_contains($query, 'ausente') ||
                str_contains($query, 'faltó') ||
                str_contains($query, 'asistieron') ||
                str_contains($query, 'llegó') ||
                str_contains($query, 'llegaron')
            ) {

                Log::info('Detectada consulta de tipo: asistencia');

                // Obtener datos de asistencia con diferentes campos posibles
                $totalPersons = personal::count();

                // Intentar diferentes campos de fecha para asistencia
                $todayAttendance = asistencia::where(function ($query) use ($today) {
                    $query->whereDate('created_at', $today)
                        ->orWhereDate('fecha', $today);
                })->count();

                $attendancePercentage = $totalPersons > 0 ? round(($todayAttendance / $totalPersons) * 100) : 0;

                // Obtener datos de la semana
                $weekStart = Carbon::now()->startOfWeek();
                $weekEnd = Carbon::now()->endOfWeek();
                $weekAttendance = asistencia::where(function ($query) use ($weekStart, $weekEnd) {
                    $query->whereBetween('created_at', [$weekStart, $weekEnd])
                        ->orWhereBetween('fecha', [$weekStart, $weekEnd]);
                })->count();

                // Obtener datos del mes
                $monthStart = Carbon::now()->startOfMonth();
                $monthEnd = Carbon::now()->endOfMonth();
                $monthAttendance = asistencia::where(function ($query) use ($monthStart, $monthEnd) {
                    $query->whereBetween('created_at', [$monthStart, $monthEnd])
                        ->orWhereBetween('fecha', [$monthStart, $monthEnd]);
                })->count();

                // Obtener nombres de personas que asistieron hoy
                $todayAttendees = asistencia::where(function ($query) use ($today) {
                    $query->whereDate('created_at', $today)
                        ->orWhereDate('fecha', $today);
                })
                    ->join('personal', 'asistencia.personal_id', '=', 'personal.id')
                    ->pluck('personal.nombre')
                    ->take(10)
                    ->toArray();

                $attendeesText = !empty($todayAttendees) ?
                    "Personas que asistieron hoy: " . implode(', ', $todayAttendees) :
                    "No hay registros específicos de nombres para hoy";

                return "Datos de asistencia:\n" .
                    "- Total de personal registrado: {$totalPersons}\n" .
                    "- Asistencias hoy: {$todayAttendance} personas ({$attendancePercentage}%)\n" .
                    "- Asistencias esta semana: {$weekAttendance}\n" .
                    "- Asistencias este mes: {$monthAttendance}\n" .
                    "- {$attendeesText}\n";
            }

            // Consulta sobre comidas
            elseif (
                str_contains($query, 'comida') ||
                str_contains($query, 'comió') ||
                str_contains($query, 'comieron') ||
                str_contains($query, 'almuerzo') ||
                str_contains($query, 'desayuno') ||
                str_contains($query, 'cena') ||
                str_contains($query, 'merienda')
            ) {

                Log::info('Detectada consulta de tipo: comida');

                // Obtener datos de comidas con diferentes campos posibles
                $todayMeals = comida::where(function ($query) use ($today) {
                    $query->whereDate('created_at', $today)
                        ->orWhereDate('fecha', $today);
                })->count();

                // Contar por tipo de comida
                $mealTypes = comida::where(function ($query) use ($today) {
                    $query->whereDate('created_at', $today)
                        ->orWhereDate('fecha', $today);
                })
                    ->select('tipo', DB::raw('count(*) as total'))
                    ->groupBy('tipo')
                    ->pluck('total', 'tipo')
                    ->toArray();

                // Datos de la semana
                $weekStart = Carbon::now()->startOfWeek();
                $weekEnd = Carbon::now()->endOfWeek();
                $weekMeals = comida::where(function ($query) use ($weekStart, $weekEnd) {
                    $query->whereBetween('created_at', [$weekStart, $weekEnd])
                        ->orWhereBetween('fecha', [$weekStart, $weekEnd]);
                })->count();

                $mealTypesText = "";
                foreach ($mealTypes as $type => $count) {
                    $mealTypesText .= "- {$type}: {$count}\n";
                }

                return "Datos de comidas:\n" .
                    "- Comidas servidas hoy: {$todayMeals}\n" .
                    "- Comidas esta semana: {$weekMeals}\n" .
                    "Distribución por tipo hoy:\n{$mealTypesText}";
            }

            // Consulta sobre stock
            elseif (
                str_contains($query, 'stock') ||
                str_contains($query, 'inventario') ||
                str_contains($query, 'producto') ||
                str_contains($query, 'epp') ||
                str_contains($query, 'equipo')
            ) {

                Log::info('Detectada consulta de tipo: stock');

                $totalStock = \App\Models\stock::count();
                $totalQuantity = \App\Models\stock::sum('cantidad');

                // Stock agregado hoy
                $todayStock = \App\Models\stock::whereDate('created_at', $today)->count();
                $todayQuantity = \App\Models\stock::whereDate('created_at', $today)->sum('cantidad');

                // Stock por categorías (si existe el campo)
                $stockByCategory = \App\Models\stock::select('nombre', DB::raw('sum(cantidad) as total'))
                    ->groupBy('nombre')
                    ->orderBy('total', 'desc')
                    ->take(5)
                    ->pluck('total', 'nombre')
                    ->toArray();

                $categoryText = "";
                foreach ($stockByCategory as $name => $quantity) {
                    $categoryText .= "- {$name}: {$quantity} unidades\n";
                }

                return "Datos de stock:\n" .
                    "- Total de items en stock: {$totalStock}\n" .
                    "- Cantidad total: {$totalQuantity} unidades\n" .
                    "- Items agregados hoy: {$todayStock}\n" .
                    "- Cantidad agregada hoy: {$todayQuantity} unidades\n" .
                    "Top 5 productos por cantidad:\n{$categoryText}";
            }

            // Consulta sobre personal
            elseif (
                str_contains($query, 'personal') ||
                str_contains($query, 'empleado') ||
                str_contains($query, 'trabajador') ||
                str_contains($query, 'persona') ||
                str_contains($query, 'staff')
            ) {

                Log::info('Detectada consulta de tipo: personal');

                $totalPersons = personal::count();

                // Obtener departamentos si existe el campo
                $departments = personal::select('departamento', DB::raw('count(*) as total'))
                    ->whereNotNull('departamento')
                    ->groupBy('departamento')
                    ->pluck('total', 'departamento')
                    ->toArray();

                // Si la consulta menciona un nombre específico
                $nombre = $this->extractPersonName($query);
                if ($nombre) {
                    $personInfo = personal::where('nombre', 'like', "%{$nombre}%")->first();
                    if ($personInfo) {
                        $recentAttendance = asistencia::where('personal_id', $personInfo->id)
                            ->orderBy('created_at', 'desc')
                            ->take(5)
                            ->pluck('created_at')
                            ->map(function ($date) {
                                return Carbon::parse($date)->format('d/m/Y');
                            })
                            ->toArray();

                        return "Información de {$personInfo->nombre}:\n" .
                            "- ID: {$personInfo->nro_identificacion}\n" .
                            "- Departamento: " . ($personInfo->departamento ?? 'No especificado') . "\n" .
                            "- Últimas asistencias: " . implode(', ', $recentAttendance) . "\n";
                    }
                }

                $deptText = "";
                foreach ($departments as $dept => $count) {
                    $deptText .= "- {$dept}: {$count} personas\n";
                }

                return "Datos de personal:\n" .
                    "- Total de personal: {$totalPersons}\n" .
                    "Personal por departamento:\n{$deptText}";
            }

            // Si no se detecta un tipo específico, proporcionar un contexto general
            else {
                Log::info('No se detectó un tipo específico, proporcionando contexto general');

                $totalPersons = personal::count();
                $todayAttendance = asistencia::where(function ($query) use ($today) {
                    $query->whereDate('created_at', $today)
                        ->orWhereDate('fecha', $today);
                })->count();
                $attendancePercentage = $totalPersons > 0 ? round(($todayAttendance / $totalPersons) * 100) : 0;

                $todayMeals = comida::where(function ($query) use ($today) {
                    $query->whereDate('created_at', $today)
                        ->orWhereDate('fecha', $today);
                })->count();

                $totalStock = \App\Models\stock::count();
                $todayStock = \App\Models\stock::whereDate('created_at', $today)->count();

                return "Resumen general del sistema:\n" .
                    "- Total de personal: {$totalPersons}\n" .
                    "- Asistencia hoy: {$todayAttendance} personas ({$attendancePercentage}%)\n" .
                    "- Comidas servidas hoy: {$todayMeals}\n" .
                    "- Total items en stock: {$totalStock}\n" .
                    "- Items de stock agregados hoy: {$todayStock}\n" .
                    "- Fecha actual: " . $today->format('d/m/Y') . "\n";
            }
        } catch (\Exception $e) {
            Log::error('Error al obtener contexto: ' . $e->getMessage());
            Log::error('Stack trace: ' . $e->getTraceAsString());
            return "Error al obtener datos de contexto: " . $e->getMessage();
        }
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
        if (preg_match('/(?:información|datos|info) (?:de|sobre|del?) ([a-zA-ZáéíóúÁÉÍÓÚñÑ\s]+)/', $query, $matches)) {
            return trim($matches[1]);
        }

        // Buscar patrones como "[nombre] asistió" o "[nombre] faltó"
        if (preg_match('/([a-zA-ZáéíóúÁÉÍÓÚñÑ\s]+) (?:asistió|faltó|llegó|vino)/', $query, $matches)) {
            return trim($matches[1]);
        }

        // Buscar patrones como "¿dónde está [nombre]?" o "¿vino [nombre]?"
        if (preg_match('/(?:dónde está|vino|asistió) ([a-zA-ZáéíóúÁÉÍÓÚñÑ\s]+)/', $query, $matches)) {
            return trim($matches[1]);
        }

        return null;
    }

    /**
     * Método para obtener sugerencias de preguntas frecuentes
     */
    private function getSuggestedQuestions(): array
    {
        return [
            "¿Cuántas personas asistieron hoy?",
            "¿Quién faltó esta semana?",
            "¿Cuántas comidas se sirvieron hoy?",
            "¿Cuál es el stock disponible?",
            "¿Cuánto personal tenemos en total?",
            "¿Cómo estuvo la asistencia este mes?",
            "¿Qué productos necesitan reposición?",
            "¿Cuántos desayunos se sirvieron hoy?"
        ];
    }

    /**
     * Método para detectar consultas sobre reportes
     */
    private function isReportQuery(string $query): bool
    {
        $reportKeywords = [
            'reporte',
            'informe',
            'resumen',
            'estadística',
            'análisis',
            'gráfico',
            'chart',
            'dashboard',
            'métricas',
            'kpi'
        ];

        foreach ($reportKeywords as $keyword) {
            if (str_contains($query, $keyword)) {
                return true;
            }
        }

        return false;
    }
}
