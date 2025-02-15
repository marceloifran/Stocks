<?php

namespace App\Http\Controllers;

use App\Services\DeepSeekService;
use Illuminate\Http\Request;

class ChatbotController extends Controller
{
    public function __invoke(Request $request)
    {
        return view('filament.pages.chatbot');
    }
}
