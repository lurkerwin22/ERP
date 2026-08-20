<?php

namespace App\Services\Ai\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class AiAgentService
{
    protected string $apiKey;
    protected string $routerModel;
    protected string $analystModel;

    public function __construct(
        protected ToolRegistry $toolRegistry,
        protected IntentRouter $intentRouter
    ) {
        $this->apiKey       = config('services.groq.api_key');
        $this->routerModel  = config('services.groq.router_model', 'openai/gpt-oss-20b');
        $this->analystModel = config('services.groq.analyst_model', 'qwen/qwen3.6-27b');
    }

    public function chat(array $messages): array
    {
        $lastUserMessage = collect($messages)->where('role', 'user')->last()['content'] ?? '';

        // 1. Phase 2: Instant PHP Regex Router (0 tokens)
        if ($localResponse = $this->intentRouter->matchAndExecute($lastUserMessage)) {
            return $localResponse;
        }

        // 2. Phase 3: Lightweight Model for Tool Identification
        $toolCall = $this->extractToolWithSmallModel($messages);

        if (!$toolCall) {
            // General conversational query: reply directly using the fast model
            return $this->sendCompletion($this->routerModel, $messages);
        }

        // Execute identified ERP Tool
        $toolResult = $this->toolRegistry->execute($toolCall['name'], $toolCall['args']);

        // Check if the user prompt asks for analysis/advice
        if ($this->requiresDeepAnalysis($lastUserMessage)) {
            // Forward data to heavy model for strategy/analysis
            return $this->synthesizeWithAnalystModel($messages, $toolCall['name'], $toolResult);
        }

        // Simple lookup: return formatted template without calling heavy LLM
        return [
            'role' => 'assistant',
            'content' => $this->formatSimpleToolOutput($toolCall['name'], $toolResult)
        ];
    }

    protected function extractToolWithSmallModel(array $messages): ?array
    {
        $response = Http::withHeaders(['Authorization' => 'Bearer ' . $this->apiKey])
            ->post('https://api.groq.com/openai/v1/chat/completions', [
                'model'    => $this->routerModel,
                'messages' => $messages,
                'tools'    => $this->toolRegistry->getTools(),
                'tool_choice' => 'auto',
            ]);

        $message = $response->json('choices.0.message');

        if (!empty($message['tool_calls'][0])) {
            $call = $message['tool_calls'][0]['function'];
            return [
                'name' => $call['name'],
                'args' => json_decode($call['arguments'] ?? '{}', true),
            ];
        }

        return null;
    }

    protected function requiresDeepAnalysis(string $prompt): bool
    {
        $prompt = mb_strtolower($prompt);

        $keywords = [
            // French Strategy Keywords
            'analyse', 'pourquoi', 'stratégie', 'conseil', 'recommandation', 'optimiser', 'expliquer', 'comment', 'augmenter',
            // English Strategy Keywords
            'increase', 'increament', 'grow', 'boost', 'how', 'what', 'should', 'recommend', 'advice', 'strategy', 'improve'
        ];

        $pattern = '/(' . implode('|', $keywords) . ')/u';

        return (bool) preg_match($pattern, $prompt);
    }

    protected function synthesizeWithAnalystModel(array $messages, string $toolName, array $data): array
    {
        $messages[] = [
            'role' => 'system',
            'content' => "Données extraites de l'outil {$toolName}: " . json_encode($data) . "\nAnalyse ces données et réponds de façon concise et stratégique."
        ];

        return $this->sendCompletion($this->analystModel, $messages);
    }

    protected function sendCompletion(string $model, array $messages): array
    {
        $res = Http::retry(2, 1000)
            ->withHeaders(['Authorization' => 'Bearer ' . $this->apiKey])
            ->post('https://api.groq.com/openai/v1/chat/completions', [
                'model'    => $model,
                'messages' => $messages,
            ]);

        return [
            'role' => 'assistant',
            'content' => $res->json('choices.0.message.content', 'Une erreur est survenue.')
        ];
    }

    protected function formatSimpleToolOutput(string $toolName, array $data): string
    {
        return "```json\n" . json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) . "\n```";
    }
}