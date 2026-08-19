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
            'messages.*.role' => 'required|string',
            'messages.*.content' => 'nullable|string', // Permet le null depuis le front-end
        ]);

        try {
            // 1. Gather Live Telemetry & Business Rules
            $telemetry = $this->getLiveTelemetry();
            $proactiveAlerts = $this->aiService->getProactiveAlerts();

            // 2. Build Deep Business Context & System Instructions
            $systemPrompt = [
                'role' => 'system',
                'content' => "Vous êtes l'Assistant de Direction ERP.\n"
                    . "RÈGLES D'AFFAIRES ET IMPÉRATIFS :\n"
                    . "1. Devise : TND. Soyez concis, direct et axé sur les chiffres clés (max 2 à 4 phrases ou courtes puces).\n"
                    . "2. N'exigez JAMAIS d'identifiant client, de code entreprise ou de filtres inutiles. Exploitez directement les outils et la télémétrie.\n"
                    . "3. Si un problème est détecté (rupture de stock, impayé élevé), proposez immédiatement une action corrective concrète.\n\n"
                    . "TÉLÉMÉTRIE EN TEMPS RÉEL (DASHBOARD) :\n" . json_encode($telemetry) . "\n"
                    . "ALERTES PROACTIVES :\n" . json_encode($proactiveAlerts)
            ];

            // 3. Define Expanded Read-Only Function Tools
            $tools = [
                [
                    'type' => 'function',
                    'function' => [
                        'name' => 'get_sales_analytics',
                        'description' => 'Retrieve sales analytics, revenue, and top products sold for a given timeframe.',
                        'parameters' => [
                            'type' => 'object',
                            'properties' => [
                                'days' => [
                                    'type' => 'integer',
                                    'description' => 'Number of past days to analyze (default: 7)',
                                ],
                            ],
                        ],
                    ],
                ],
                [
                    'type' => 'function',
                    'function' => [
                        'name' => 'get_low_stock_products',
                        'description' => 'Retrieve products where stock quantity is at or below the threshold.',
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
                ],
                [
                    'type' => 'function',
                    'function' => [
                        'name' => 'get_overdue_debtors',
                        'description' => 'Retrieve clients with unpaid invoices or outstanding debts above a specified amount.',
                        'parameters' => [
                            'type' => 'object',
                            'properties' => [
                                'min_amount' => [
                                    'type' => 'number',
                                    'description' => 'Minimum unpaid debt in TND to flag (default: 1000)',
                                ],
                            ],
                        ],
                    ],
                ],
                [
                    'type' => 'function',
                    'function' => [
                        'name' => 'get_product_performance',
                        'description' => 'Get a list of top-selling products, including total quantities sold and total revenue generated. Can be filtered by date.',
                        'parameters' => [
                            'type' => 'object',
                            'properties' => [
                                'limit' => [
                                    'type' => 'integer',
                                    'description' => 'The number of top products to return (default: 5)',
                                ],
                                'start_date' => [
                                    'type' => 'string',
                                    'description' => 'Start date for the performance period in YYYY-MM-DD format (optional)',
                                ],
                                'end_date' => [
                                    'type' => 'string',
                                    'description' => 'End date for the performance period in YYYY-MM-DD format (optional)',
                                ],
                            ],
                        ],
                    ],
                ],
                [
                    'type' => 'function',
                    'function' => [
                        'name' => 'get_business_summary',
                        'description' => 'Get a high-level macro financial snapshot of the business, including total revenue invoiced, total cash collected, and total outstanding debt.',
                        'parameters' => [
                            'type' => 'object',
                            'properties' => (object) [], 
                        ],
                    ],
                ],
            ];

            // S'assurer qu'aucun message précédent n'a de contenu "null"
            $safeMessages = array_map(function ($msg) {
                if (!isset($msg['content']) || $msg['content'] === null) {
                    $msg['content'] = "";
                }
                return $msg;
            }, $validated['messages']);

            $messages = array_merge([$systemPrompt], $safeMessages);

            // 4. Primary API Call to Groq Endpoint
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . env('GROQ_API_KEY'),
                'Content-Type' => 'application/json',
            ])->timeout(60)->post('https://api.groq.com/openai/v1/chat/completions', [
                'model' => 'openai/gpt-oss-120b',
                'messages' => $messages,
                'tools' => $tools,
                'tool_choice' => 'auto',
                'temperature' => 0.2,
                'max_tokens' => 400,
            ]);

            if ($response->failed()) {
                Log::error('Groq API Error: ' . $response->body());
                $errorMsg = $response->json()['error']['message'] ?? 'Service temporairement indisponible.';
                return response()->json(['message' => 'Erreur API: ' . $errorMsg], 422);
            }

            $responseData = $response->json();
            $choice = $responseData['choices'][0]['message'] ?? null;

            if (!$choice) {
                return response()->json(['reply' => 'Aucune réponse générée par l\'IA.']);
            }

            // CORRECTION CRITIQUE : Évite que l'API ne crashe sur le 2ème appel si 'content' est null
            if (!isset($choice['content']) || $choice['content'] === null) {
                $choice['content'] = "";
            }

            // 5. Handle Tool Execution
            if (isset($choice['tool_calls']) && is_array($choice['tool_calls'])) {
                $messages[] = $choice;

                foreach ($choice['tool_calls'] as $toolCall) {
                    $functionName = $toolCall['function']['name'];
                    $args = json_decode($toolCall['function']['arguments'] ?? '{}', true) ?? [];

                    $toolResult = $this->executeTool($functionName, $args);
                    // LOG RAW DATA TO verify ground truth in storage/logs/laravel.log
                    Log::info("Tool execution output for {$functionName}:", [
                        'args' => $args,
                        'result' => $toolResult
                    ]);
                    $messages[] = [
                        'role' => 'tool',
                        'tool_call_id' => $toolCall['id'],
                        'name' => $functionName,
                        'content' => json_encode($toolResult),
                    ];
                }

                // 6. Secondary Call to Synthesize Final Reply with Tool Outputs
                $finalResponse = Http::withHeaders([
                    'Authorization' => 'Bearer ' . env('GROQ_API_KEY'),
                    'Content-Type' => 'application/json',
                ])->timeout(60)->post('https://api.groq.com/openai/v1/chat/completions', [
                    'model' => 'openai/gpt-oss-120b',
                    'messages' => $messages,
                    'temperature' => 0.2,
                    'max_tokens' => 400,
                ]);

                if ($finalResponse->failed()) {
                    Log::error('Groq Synthesis Error: ' . $finalResponse->body());
                    return response()->json(['message' => 'Erreur lors de la génération de la réponse.'], 500);
                }

                $rawContent = $finalResponse->json()['choices'][0]['message']['content'] ?? null;
                
                $reply = !empty(trim($rawContent ?? '')) 
                    ? $rawContent 
                    : 'Les données ont été récupérées, mais je n\'ai pas pu générer un résumé.';

                return response()->json(['reply' => $reply]);
            }

            // Return response for queries without tool calls
            return response()->json(['reply' => $choice['content']]);

        } catch (\Throwable $e) {
            Log::error('AiAssistantController Exception: ' . $e->getMessage());
            return response()->json(['message' => 'Erreur serveur: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Gather light aggregate stats to give instant context without tool calls.
     */
    private function getLiveTelemetry(): array
    {
        try {
            return [
                'today_sales_total' => \App\Models\SaleItem::whereDate('created_at', today())->sum('total_price'),
                'critical_stock_count' => \App\Models\Product::where('stock_quantity', '<=', 10)->count(),
                'total_products_count' => \App\Models\Product::count(),
            ];
        } catch (\Throwable $e) {
            Log::warning('Telemetry gather failed: ' . $e->getMessage());
            return ['status' => 'Telemetry offline'];
        }
    }

    /**
     * Execute strictly read-only Laravel Eloquent queries safely.
     */
    private function executeTool(string $name, array $args): array
    {
        try {
            return match ($name) {
                'get_sales_analytics' => [
                    'days' => $args['days'] ?? 7,
                    'recent_items' => \App\Models\SaleItem::with('product:id,name')
                        ->where('created_at', '>=', now()->subDays($args['days'] ?? 7))
                        ->latest()
                        ->take(10)
                        ->get(['id', 'product_id', 'quantity', 'unit_price', 'total_price', 'created_at'])
                        ->toArray(),
                ],

                // Détection basée sur 'stock' et comparaison avec 'alert_threshold'
                'get_low_stock_products' => \App\Models\Product::whereColumn('stock', '<=', 'alert_threshold')
                    ->orWhere('stock', '<=', $args['threshold'] ?? 10)
                    ->orderBy('stock', 'asc')
                    ->take(10)
                    ->get(['id', 'name', 'stock', 'alert_threshold', 'price'])
                    ->toArray(),

                // Calcul dynamique des dettes clients
                'get_overdue_debtors' => \App\Models\Customer::select('customers.id', 'customers.name', 'customers.email', 'customers.phone')
                    ->selectRaw('SUM(sales.total) as outstanding_debt')
                    ->join('sales', 'customers.id', '=', 'sales.customer_id')
                    ->where('sales.status', '!=', 'cancelled')
                    ->groupBy('customers.id', 'customers.name', 'customers.email', 'customers.phone')
                    ->having('outstanding_debt', '>=', $args['min_amount'] ?? 1000)
                    ->orderByDesc('outstanding_debt')
                    ->take(5)
                    ->get()
                    ->toArray(),

                'get_product_performance' => \App\Models\Product::query()
                    ->select(
                        'products.id',
                        'products.name',
                        \Illuminate\Support\Facades\DB::raw('SUM(sale_items.quantity) as total_quantity_sold'),
                        \Illuminate\Support\Facades\DB::raw('SUM(sale_items.total_price) as total_revenue') 
                    )
                    ->join('sale_items', 'products.id', '=', 'sale_items.product_id')
                    ->join('sales', 'sales.id', '=', 'sale_items.sale_id')
                    ->where('sales.status', '!=', 'cancelled')
                    ->when(!empty($args['start_date']), fn($q) => $q->whereDate('sales.sale_date', '>=', $args['start_date']))
                    ->when(!empty($args['end_date']), fn($q) => $q->whereDate('sales.sale_date', '<=', $args['end_date']))
                    ->groupBy('products.id', 'products.name')
                    ->orderByDesc('total_revenue')
                    ->limit($args['limit'] ?? 5)
                    ->get()
                    ->toArray(),

                // Synthèse financière basée sur 'sales.total'
                'get_business_summary' => [
                    'total_invoiced_all_time' => round(\App\Models\Sale::where('status', '!=', 'cancelled')->sum('total') ?? 0, 3),
                    'total_collected_all_time' => round(\App\Models\Payment::sum('amount') ?? 0, 3),
                    'total_outstanding_debt' => round(
                        (\App\Models\Sale::where('status', '!=', 'cancelled')->sum('total') ?? 0) - (\App\Models\Payment::sum('amount') ?? 0), 
                        3
                    ),
                    'current_month_invoiced' => round(
                        \App\Models\Sale::where('status', '!=', 'cancelled')
                            ->whereMonth('sale_date', now()->month)
                            ->whereYear('sale_date', now()->year)
                            ->sum('total') ?? 0, 
                        3
                    ),
                    'active_customers_count' => \App\Models\Customer::has('sales')->count(),
                ],

                default => ['error' => 'Action inconnue'],
            };
        } catch (\Throwable $e) {
            \Illuminate\Support\Facades\Log::error("Tool execution error [{$name}]: " . $e->getMessage());
            return ['error' => 'Query execution failed: ' . $e->getMessage()];
        }
    }
}