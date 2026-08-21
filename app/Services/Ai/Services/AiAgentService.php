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
        $isAnalysisQuery = $this->requiresDeepAnalysis($lastUserMessage);

        // 1. Phase 2: Instant PHP Regex Router (0 tokens)
        // Skip IntentRouter if the query requires strategy, advice, or deep analysis
        if (!$isAnalysisQuery && ($localResponse = $this->intentRouter->matchAndExecute($lastUserMessage))) {
            return $localResponse;
        }

        // 2. Phase 3: Tool Identification via Router LLM
        $toolCall = $this->extractToolWithSmallModel($messages);

        if (!$toolCall) {
            // Conversational/Advisory query without tool execution
            return $this->sendCompletion($this->analystModel, $messages, $this->getSystemPrompt());
        }

        // Execute identified ERP Tool
        $toolResult = $this->toolRegistry->execute($toolCall['name'], $toolCall['args']);

        // Strategic query: pass tool results to heavy model for growth advice
        if ($isAnalysisQuery) {
            return $this->synthesizeWithAnalystModel($messages, $toolCall['name'], $toolResult);
        }

        // Simple data lookup
        return [
            'role' => 'assistant',
            'content' => $this->formatSimpleToolOutput($toolCall['name'], $toolResult)
        ];
    }

    protected function extractToolWithSmallModel(array $messages): ?array
    {
        $formattedMessages = array_merge([
            ['role' => 'system', 'content' => $this->getSystemPrompt()]
        ], $messages);

        $response = Http::withHeaders(['Authorization' => 'Bearer ' . $this->apiKey])
            ->post('https://api.groq.com/openai/v1/chat/completions', [
                'model'       => $this->routerModel,
                'messages'    => $formattedMessages,
                'tools'       => $this->toolRegistry->getTools(),
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
            'analyse', 'pourquoi', 'stratégie', 'conseil', 'recommandation', 'optimiser', 'expliquer', 'comment', 'augmenter', 'améliorer',
            // English Strategy Keywords
            'increase', 'increment', 'grow', 'boost', 'how', 'what', 'should', 'recommend', 'advice', 'strategy', 'improve', 'target'
        ];

        $pattern = '/\b(' . implode('|', $keywords) . ')\b/u';

        return (bool) preg_match($pattern, $prompt);
    }

    protected function synthesizeWithAnalystModel(array $messages, string $toolName, array $data): array
    {
        $contextMessage = [
            'role' => 'system',
            'content' => "Tu es un expert en gestion ERP et stratégie commerciale.\n" .
                         "Données extraites de l'outil '{$toolName}': " . json_encode($data, JSON_UNESCAPED_UNICODE) . "\n" .
                         "Analyse ces données et réponds à la question de l'utilisateur avec des conseils stratégiques et des actions concrètes."
        ];

        $payload = array_merge([['role' => 'system', 'content' => $this->getSystemPrompt()]], $messages, [$contextMessage]);

        return $this->sendCompletion($this->analystModel, $payload);
    }

    protected function sendCompletion(string $model, array $messages, ?string $systemPrompt = null): array
    {
        if ($systemPrompt) {
            array_unshift($messages, ['role' => 'system', 'content' => $systemPrompt]);
        }

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

    protected function getSystemPrompt(): string
    {
        return "Tu es un assistant virtuel ERP intelligent. Ton rôle est d'aider les utilisateurs à consulter leurs données (ventes, stocks, dettes) et à leur fournir des conseils stratégiques pour développer leur entreprise.";
    }

    protected function formatSimpleToolOutput(string $toolName, array $data): string
    {
        return "```json\n" . json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) . "\n```";
    }
}