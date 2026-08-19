<?php

namespace App\Http\Controllers\Ai;

use App\Http\Controllers\Controller;
use App\Services\Ai\AiAssistantService;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;


class AiAssistantController extends Controller
{
    protected AiAssistantService $aiService;

    public function __construct(AiAssistantService $aiService)
    {
        $this->aiService = $aiService;
    }

    /**
     * Display the AI Assistant view page.
     */
    public function index(): View
    {
        $alerts = $this->aiService->getProactiveAlerts();

        return view('ai.index', compact('alerts'));
    }

    /**
     * Handle chat messages via AJAX / Fetch API.
     */
    public function chat(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'messages' => 'required|array',
        ]);

        // 1. Define Available Read-Only Tools
        $tools = [
            [
                'type' => 'function',
                'function' => [
                    'name' => 'get_recent_sales',
                    'description' => 'Retrieve the list of recent sales or sold products within a given number of days.',
                    'parameters' => [
                        'type' => 'object',
                        'properties' => [
                            'days' => [
                                'type' => 'integer',
                                'description' => 'Number of past days to query sales for (default: 3)',
                            ],
                        ],
                    ],
                ],
            ],
            [
                'type' => 'function',
                'function' => [
                    'name' => 'get_low_stock_products',
                    'description' => 'Retrieve products where quantity is below safety stock threshold.',
                    'parameters' => [
                        'type' => 'object',
                        'properties' => [
                            'threshold' => [
                                'type' => 'integer',
                                'description' => 'Minimum stock threshold (default: 10)',
                            ],
                        ],
                    ],
                ],
            ]
        ];

        $systemPrompt = [
            'role' => 'system',
            'content' => "Vous êtes l'assistant ERP. Répondez de manière directe et ultra-concise (2-3 phrases max). N'hésitez pas à exécuter un outil si vous avez besoin de données en temps réel."
        ];

        $messages = array_merge([$systemPrompt], $validated['messages']);

        // 2. Call LLM with Tools enabled
        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . env('GROQ_API_KEY'),
            'Content-Type' => 'application/json',
        ])->post('https://api.groq.com/openai/v1/chat/completions', [
            'model' => 'openai/gpt-oss-120b',
            'messages' => $messages,
            'tools' => $tools,
            'tool_choice' => 'auto',
        ]);

        $responseData = $response->json();
        $choice = $responseData['choices'][0]['message'] ?? null;

        // 3. Check if the LLM wants to execute a tool call
        if (isset($choice['tool_calls'])) {
            foreach ($choice['tool_calls'] as $toolCall) {
                $functionName = $toolCall['function']['name'];
                $args = json_decode($toolCall['function']['arguments'], true);

                // Execute local read-only query
                $toolResult = $this->executeTool($functionName, $args);

                // Append assistant tool request + tool execution result to history
                $messages[] = $choice;
                $messages[] = [
                    'role' => 'tool',
                    'tool_call_id' => $toolCall['id'],
                    'content' => json_encode($toolResult),
                ];
            }

            // 4. Send updated conversation history back to LLM to produce final reply
            $finalResponse = Http::withHeaders([
                'Authorization' => 'Bearer ' . env('GROQ_API_KEY'),
                'Content-Type' => 'application/json',
            ])->post('https://api.groq.com/openai/v1/chat/completions', [
                'model' => 'openai/gpt-oss-120b',
                'messages' => $messages,
            ]);

            $reply = $finalResponse->json()['choices'][0]['message']['content'];
            return response()->json(['reply' => $reply]);
        }

        return response()->json(['reply' => $choice['content'] ?? 'Aucune réponse.']);
    }

    /**
     * Execute strictly read-only Laravel Eloquent queries
     */
    private function executeTool(string $name, array $args): array
    {
        return match ($name) {
            'get_recent_sales' => \App\Models\SaleItem::with('product')
                ->where('created_at', '>=', now()->subDays($args['days'] ?? 3))
                ->latest()
                ->take(10)
                ->get(['product_id', 'quantity', 'unit_price', 'created_at'])
                ->toArray(),

            'get_low_stock_products' => \App\Models\Product::where('stock_quantity', '<=', $args['threshold'] ?? 10)
                ->get(['id', 'name', 'stock_quantity', 'sku'])
                ->toArray(),

            default => ['error' => 'Unknown function call'],
        };
    }
}