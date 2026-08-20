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

    public function chat(Request $request, AiAgentService $aiAgentService)
    {
        $inputMessages = $request->input('messages');

        // Fallback if JS sends 'message' string instead of 'messages' array
        if (!$inputMessages && $request->has('message')) {
            $inputMessages = [
                ['role' => 'user', 'content' => $request->input('message')]
            ];
        }

        $request->merge(['messages' => $inputMessages]);

        $validated = $request->validate([
            'messages' => 'required|array',
        ]);

        $response = $aiAgentService->chat($validated['messages']);

        return response()->json($response);
    }
}