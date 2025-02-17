<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\DeepSeekService;
use App\Services\ResendMailService;

class ChatbotController extends Controller
{
    public function __invoke(Request $request)
    {
        return view('filament.pages.chatbot');
    }

    public function sendTestEmail(ResendMailService $resendMailService)
    {
        $to = "destinatario@ejemplo.com";
        $subject = "Correo de prueba con Resend";
        $htmlContent = "<h1>Hola!</h1><p>Este es un correo de prueba enviado con Resend en Laravel.</p>";

        $response = $resendMailService->sendMail($to, $subject, $htmlContent);

        return response()->json($response);
    }
}
