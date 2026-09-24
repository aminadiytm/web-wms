<?php

namespace App\Http\Controllers;

use App\Services\ChatbotService;
use Illuminate\Http\Request;
use Illuminate\Database\QueryException;

class ChatbotController extends Controller
{
    public function message(Request $request, ChatbotService $chatbot)
    {
        $data = $request->validate([
            'message' => ['required', 'string', 'max:500'],
            'product' => ['nullable', 'string', 'max:100'],
            'warehouse' => ['nullable', 'string', 'max:100'],
        ]);
        try {
            return response()->json($chatbot->reply($data));
        } catch (QueryException $exception) {
            report($exception);
            return response()->json(['message' => 'Data belum dapat dibaca. Silakan coba lagi atau hubungi administrator.'], 503);
        }
    }
}
