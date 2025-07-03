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

            // Primero, obtener la estructura de la base de datos para que Gemini pueda generar SQL apropiado
            $dbStructure = $this->getDatabaseStructure();
            Log::info('Estructura de la base de datos obtenida');

            // Obtener información del contexto básico
            Log::info('Obteniendo contexto básico para la consulta');
            $basicContext = $this->getBasicContext();

            // Preparar el mensaje para Gemini
            Log::info('Preparando datos para la API de Gemini');

            // Instrucciones del sistema como parte del mensaje del usuario
            $systemInstruction = 'Eres un asistente especializado en analizar datos de personal, asistencias, comidas y stock de una empresa. ' .
                'Basado en la pregunta del usuario, debes generar una consulta SQL adecuada, ejecutarla, y luego responder con los resultados. ' .
                'Responde de manera concisa y directa. Usa los datos proporcionados para dar respuestas precisas.';

            // Instrucciones para generar SQL
            $sqlInstruction = "Basado en mi pregunta, genera una consulta SQL apropiada usando la estructura de base de datos proporcionada. " .
                "Si no puedes generar una consulta SQL adecuada, responde directamente con la información básica disponible.";

            $data = [
                'contents' => [
                    [
                        'role' => 'user',
                        'parts' => [
                            ['text' => $systemInstruction . "\n\n" .
                                "ESTRUCTURA DE LA BASE DE DATOS:\n" . $dbStructure . "\n\n" .
                                "CONTEXTO BÁSICO:\n" . $basicContext . "\n\n" .
                                "MI PREGUNTA: " . $query . "\n\n" .
                                $sqlInstruction]
                        ]
                    ]
                ],
                'generationConfig' => [
                    'temperature' => 0.2, // Temperatura más baja para respuestas más precisas
                    'maxOutputTokens' => 800, // Aumentar para permitir respuestas más detalladas
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

                    // Intentar extraer y ejecutar SQL si está presente
                    $sqlResult = $this->extractAndExecuteSQL($content);
                    if (!empty($sqlResult)) {
                        // Si se ejecutó SQL con éxito, enviar los resultados a Gemini para interpretación
                        $finalResponse = $this->interpretSQLResults($query, $sqlResult);
                        Log::info('SQL ejecutado y resultados interpretados');
                        Log::info('====== FIN DE PROCESAMIENTO DE QUERY EN API (ÉXITO CON SQL) ======');
                        return $finalResponse;
                    }

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

    /**
     * Obtiene la estructura de las tablas principales de la base de datos
     */
    private function getDatabaseStructure(): string
    {
        $structure = "";

        // Obtener estructura de la tabla personal
        $structure .= "Tabla 'personal':\n";
        $structure .= "- id: int (clave primaria)\n";
        $structure .= "- nombre: string (nombre completo de la persona)\n";
        $structure .= "- dni: string (documento de identidad)\n";
        $structure .= "- nro_identificacion: string (número de identificación interno)\n";
        $structure .= "- departamento: string (departamento al que pertenece)\n";
        $structure .= "- presente: boolean (indica si está presente hoy)\n\n";

        // Obtener estructura de la tabla asistencia
        $structure .= "Tabla 'asistencia':\n";
        $structure .= "- id: int (clave primaria)\n";
        $structure .= "- personal_id: int (clave foránea a personal.id)\n";
        $structure .= "- fecha: datetime (fecha y hora de registro)\n";
        $structure .= "- estado: string (entrada/salida)\n\n";

        // Obtener estructura de la tabla comida
        $structure .= "Tabla 'comidas':\n";
        $structure .= "- id: int (clave primaria)\n";
        $structure .= "- personal_id: int (clave foránea a personal.id)\n";
        $structure .= "- fecha: datetime (fecha y hora de registro)\n";
        $structure .= "- tipo_comida: string (desayuno/almuerzo/merienda/cena)\n\n";

        // Obtener estructura de la tabla stock_movement
        $structure .= "Tabla 'stock_movement':\n";
        $structure .= "- id: int (clave primaria)\n";
        $structure .= "- personal_id: int (clave foránea a personal.id)\n";
        $structure .= "- stock_id: int (clave foránea a stock.id)\n";
        $structure .= "- cantidad: int (cantidad entregada)\n";
        $structure .= "- fecha_movimiento: datetime (fecha de entrega)\n";

        return $structure;
    }

    /**
     * Obtiene un contexto básico con información general
     */
    private function getBasicContext(): string
    {
        $today = Carbon::today();

        $totalPersons = personal::count();
        $deptCount = personal::select('departamento')
            ->whereNotNull('departamento')
            ->distinct()
            ->count();

        $todayAttendance = asistencia::whereDate('fecha', $today)
            ->where('estado', 'entrada')
            ->count();

        $todayMeals = Comida::whereDate('fecha', $today)->count();

        return "- Total de personal: $totalPersons\n" .
               "- Número de departamentos: $deptCount\n" .
               "- Asistencias registradas hoy: $todayAttendance\n" .
               "- Comidas servidas hoy: $todayMeals\n";
    }

    /**
     * Extrae y ejecuta consultas SQL de la respuesta de Gemini
     */
    private function extractAndExecuteSQL(string $content): string
    {
        // Buscar consultas SQL en la respuesta
        if (preg_match('/```sql\s*(.*?)\s*```/s', $content, $matches) ||
            preg_match('/`(SELECT.*?)`/is', $content, $matches) ||
            preg_match('/(SELECT.*?);/is', $content, $matches)) {

            $sql = trim($matches[1]);
            Log::info('SQL extraído: ' . $sql);

            try {
                // Ejecutar la consulta con límite de seguridad
                $sql = $this->sanitizeSQL($sql);
                Log::info('SQL sanitizado: ' . $sql);

                $results = DB::select($sql);
                Log::info('Consulta SQL ejecutada con éxito. Resultados: ' . count($results));

                // Convertir resultados a formato legible
                return json_encode($results, JSON_PRETTY_PRINT);

            } catch (\Exception $e) {
                Log::error('Error al ejecutar SQL: ' . $e->getMessage());
                return '';
            }
        }

        return '';
    }

    /**
     * Sanitiza la consulta SQL para mayor seguridad
     */
    private function sanitizeSQL(string $sql): string
    {
        // Asegurarse de que sea solo una consulta SELECT
        if (!preg_match('/^SELECT/i', $sql)) {
            throw new \Exception("Solo se permiten consultas SELECT");
        }

        // Prohibir modificaciones a la base de datos
        if (preg_match('/\b(UPDATE|DELETE|DROP|INSERT|ALTER|CREATE|TRUNCATE)\b/i', $sql)) {
            throw new \Exception("Solo se permiten consultas de lectura");
        }

        // Limitar resultados para evitar sobrecarga
        if (!preg_match('/\bLIMIT\s+\d+\b/i', $sql)) {
            $sql .= ' LIMIT 50';
        }

        return $sql;
    }

    /**
     * Envía los resultados SQL a Gemini para interpretación
     */
    private function interpretSQLResults(string $query, string $sqlResults): string
    {
        try {
            Log::info('Interpretando resultados SQL con Gemini');

            // Preparar el mensaje para Gemini
            $data = [
                'contents' => [
                    [
                        'role' => 'user',
                        'parts' => [
                            ['text' => "Soy un asistente de IA y he ejecutado una consulta SQL para responder a esta pregunta: \"$query\". " .
                                "Estos son los resultados de la consulta SQL:\n\n$sqlResults\n\n" .
                                "Por favor, interpreta estos resultados y proporciona una respuesta clara y concisa a la pregunta original. " .
                                "No menciones que has ejecutado SQL o datos técnicos, solo responde directamente a la pregunta."]
                        ]
                    ]
                ],
                'generationConfig' => [
                    'temperature' => 0.3,
                    'maxOutputTokens' => 500,
                    'topP' => 0.8,
                    'topK' => 40
                ]
            ];

            // Llamar a la API de Gemini
            $apiKey = config('services.gemini.api_key');
            $model = config('services.gemini.model', 'gemini-1.5-flash');
            $url = "https://generativelanguage.googleapis.com/v1beta/models/{$model}:generateContent?key={$apiKey}";

            $response = Http::withHeaders([
                'Content-Type' => 'application/json',
            ])->post($url, $data);

            if ($response->successful() &&
                isset($response->json()['candidates'][0]['content']['parts'][0]['text'])) {
                return $response->json()['candidates'][0]['content']['parts'][0]['text'];
            }

            // Si falla la interpretación, devolver los resultados sin procesar
            return "Resultados de la consulta:\n" . $sqlResults;

        } catch (\Exception $e) {
            Log::error('Error al interpretar resultados SQL: ' . $e->getMessage());
            return "Resultados de la consulta:\n" . $sqlResults;
        }
    }
}
