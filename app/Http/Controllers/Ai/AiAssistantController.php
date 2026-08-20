<?php

namespace App\Http\Controllers\Ai;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Customer;
use App\Services\Ai\Services\AiAgentService;
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

        return view('ai.index', compact('alerts'));
    }

    public function chat(Request $request)
    {
        $validated = $request->validate([
            'messages' => 'required|array',
            'messages.*.role' => 'required|string',
            'messages.*.content' => 'nullable|string',
        ]);

        $responseMessage = $this->agentService->chat($validated['messages']);

        $content = $responseMessage['content'] ?? 'No response generated.';

        return response()->json([
            'role' => 'assistant',
            'content' => $content,
            'reply' => $content, // Satisfies app.js (data.reply)
        ]);
    }
}