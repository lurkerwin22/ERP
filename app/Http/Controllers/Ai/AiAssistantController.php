<?php

namespace App\Http\Controllers\Ai;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Customer;
use App\Services\Ai\Services\AiAgentService;
use App\Models\ChatMessage;
use Illuminate\Http\Request;

class AiAssistantController extends Controller
{
    protected AiAgentService $agentService;

    public function __construct(AiAgentService $agentService)
    {
        $this->agentService = $agentService;
    }

    public function index()
    {
        $alerts = [
            'low_stock_count' => Product::whereColumn('stock', '<=', 'alert_threshold')->count(),
            'high_debt_customers' => Customer::all()->filter(fn ($c) => $c->total_outstanding_debt > 0)->count(),
        ];

        // Fetch past chat history to render on load
        $messages = ChatMessage::oldest()->take(50)->get();

        return view('ai.index', compact('alerts', 'messages'));
    }

    public function chat(Request $request, AiAgentService $aiAgentService)
    {
        $inputMessages = $request->input('messages');

        if (!$inputMessages && $request->has('message')) {
            $inputMessages = [
                ['role' => 'user', 'content' => $request->input('message')]
            ];
        }

        $request->merge(['messages' => $inputMessages]);

        $validated = $request->validate([
            'messages' => 'required|array',
        ]);

        $userContent = $validated['messages'][0]['content'];

        $response = $aiAgentService->chat($validated['messages']);
        $aiContent = $response['content'] ?? 'Une erreur est survenue.';

        // Save strictly the cleaned assistant response
        ChatMessage::create([
            'role' => 'assistant',
            'content' => $aiContent,
        ]);

        return response()->json([
            'role' => 'assistant',
            'content' => $aiContent
        ]);
    }
}