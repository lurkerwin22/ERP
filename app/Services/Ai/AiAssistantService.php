<?php

namespace App\Services\Ai;

use App\Services\Ai\Tools\StockAnalysisTool;
use App\Services\Ai\Tools\SalesAnalysisTool;
use App\Services\Ai\Tools\CustomerDebtTool;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class AiAssistantService
{
    protected string $apiKey;

    protected string $baseUrl = 'https://api.groq.com/openai/v1/chat/completions';
    protected string $model = 'openai/gpt-oss-20b';

    public function __construct()
    {
        $this->apiKey = config('services.groq.api_key', env('GROQ_API_KEY', ''));
    }

    /**
     * Map of tool function names to their concrete classes
     */
    protected function getToolRegistry(): array
    {
        return [
            'get_low_stock_products' => [StockAnalysisTool::class, 'execute'],
            'get_sales_summary'     => [SalesAnalysisTool::class, 'execute'],
            'get_customer_debts'    => [CustomerDebtTool::class, 'execute'],
        ];
    }

    /**
     * Array of JSON schemas sent to Groq API defining available functions
     */
    protected function getToolDefinitions(): array
    {
        return [
            StockAnalysisTool::definition(),
            SalesAnalysisTool::definition(),
            CustomerDebtTool::definition(),
        ];
    }

    /**
     * Process user messages through the agentic tool-loop
     */
    public function ask(array $messages): string
    {
        if (empty($this->apiKey)) {
            return "Erreur: La clé d'API Groq n'est pas configurée dans votre fichier .env (GROQ_API_KEY).";
        }

        $systemPrompt = [
            'role' => 'system',
            'content' => "Vous êtes l'assistant IA intégré de cet ERP. " .
                         "Répondez de manière professionnelle, concise et claire. " .
                         "Formattez vos réponses avec du Markdown propre (puces, tableaux, gras). " .
                         "Utilisez toujours la monnaie TND (Dinar Tunisien) pour les valeurs financières."
        ];

        // Prepend system prompt to conversation context
        $payloadMessages = array_merge([$systemPrompt], $messages);

        try {
            // STEP A: First API Call — Ask LLM if it requires a tool to answer
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $this->apiKey,
                'Content-Type'  => 'application/json',
            ])->timeout(30)->post($this->baseUrl, [
                'model'       => $this->model,
                'messages'    => $payloadMessages,
                'tools'       => $this->getToolDefinitions(),
                'tool_choice' => 'auto',
            ]);

            if ($response->failed()) {
                Log::error('Groq API Error: ' . $response->body());
                return "Désolé, une erreur de communication avec le service IA est survenue.";
            }

            $responseData = $response->json();
            $choiceMessage = $responseData['choices'][0]['message'] ?? null;

            if (!$choiceMessage) {
                return "Aucune réponse n'a été retournée par l'assistant.";
            }

            // STEP B: Check if model decided to call tools
            if (!empty($choiceMessage['tool_calls'])) {
                // Append model's response (with tool request intent) to payload
                $payloadMessages[] = $choiceMessage;

                foreach ($choiceMessage['tool_calls'] as $toolCall) {
                    $functionName = $toolCall['function']['name'];
                    $arguments = json_decode($toolCall['function']['arguments'] ?? '{}', true) ?? [];

                    Log::info("AI requested tool execution: {$functionName}", $arguments);

                    // Execute tool against local Laravel Eloquent models
                    $toolResult = $this->dispatchTool($functionName, $arguments);

                    // Append tool execution result back to payload
                    $payloadMessages[] = [
                        'role'         => 'tool',
                        'tool_call_id' => $toolCall['id'],
                        'content'      => json_encode($toolResult),
                    ];
                }

                // STEP C: Second API Call — Synthesize database result into human response
                $finalResponse = Http::withHeaders([
                    'Authorization' => 'Bearer ' . $this->apiKey,
                    'Content-Type'  => 'application/json',
                ])->timeout(30)->post($this->baseUrl, [
                    'model'    => $this->model,
                    'messages' => $payloadMessages,
                ]);

                if ($finalResponse->failed()) {
                    Log::error('Groq API Final Call Error: ' . $finalResponse->body());
                    return "Erreur lors de la synthèse des données par l'assistant.";
                }

                $finalData = $finalResponse->json();
                return $finalData['choices'][0]['message']['content'] ?? "Erreur de génération de réponse.";
            }

            // Standard response if no tool execution was required
            return $choiceMessage['content'] ?? "Je n'ai pas pu obtenir de réponse.";

        } catch (\Exception $e) {
            Log::error('AiAssistantService Exception: ' . $e->getMessage());
            return "Une erreur interne s'est produite lors du traitement de la requête.";
        }
    }

    /**
     * Dispatch execution of selected tool
     */
    protected function dispatchTool(string $name, array $args): array
    {
        $registry = $this->getToolRegistry();

        if (isset($registry[$name])) {
            [$class, $method] = $registry[$name];
            return (new $class())->$method($args);
        }

        return ['error' => "Alerte: L'outil '{$name}' n'est pas enregistré."];
    }

    /**
     * Helper to generate proactive dashboard alerts directly from tools
     */
    public function getProactiveAlerts(): array
    {
        try {
            $lowStock = (new StockAnalysisTool())->execute(['limit' => 50]);
            $debts    = (new CustomerDebtTool())->execute([]);

            return [
                'low_stock_count'      => count($lowStock),
                'high_debt_customers' => count($debts),
            ];
        } catch (\Exception $e) {
            Log::error('Proactive Alert Error: ' . $e->getMessage());
            return [
                'low_stock_count'      => 0,
                'high_debt_customers' => 0,
            ];
        }
    }
}