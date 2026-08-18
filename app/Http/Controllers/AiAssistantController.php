<?php

namespace App\Http\Controllers;

use App\Services\Ai\AiAssistantService;
use Illuminate\Http\Request;

class AiAssistantController extends Controller
{
    public function index()
    {
        return view('ai.assistant');
    }

    public function chat(Request $request, AiAssistantService $aiService)
    {
        $request->validate([
            'message' => 'required|string|max:1000',
            'history' => 'array'
        ]);

        $userMessage = $request->input('message');
        $history = $request->input('history', []);

        // Reconstruct conversation sequence
        $messages = [];
        foreach ($history as $item) {
            $messages[] = [
                'role' => $item['role'],
                'content' => $item['content'],
            ];
        }

        $messages[] = ['role' => 'user', 'content' => $userMessage];

        $reply = $aiService->ask($messages);

        return response()->json([
            'reply' => $reply
        ]);
    }
}