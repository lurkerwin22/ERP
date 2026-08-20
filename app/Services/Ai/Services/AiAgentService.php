<?php

namespace App\Services\Ai\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class AiAgentService
{
    protected ToolRegistry $toolRegistry;
    protected string $apiKey;
    protected string $model;

    public function __construct(ToolRegistry $toolRegistry)
    {
        $this->toolRegistry = $toolRegistry;
        $this->apiKey = config('services.groq.api_key', env('GROQ_API_KEY', ''));
        $this->model = config('services.groq.model', env('GROQ_MODEL', 'qwen/qwen3.6-27b'));
    }

    public function chat(array $messages): array
    {
        $systemPrompt = [
            'role' => 'system',
            'content' => 'You are an intelligent ERP Business Assistant. Analyze data carefully and provide concise, clear answers. All monetary values are in Tunisian Dinars (TND).'
        ];

        if (empty($messages) || $messages[0]['role'] !== 'system') {
            array_unshift($messages, $systemPrompt);
        }

        for ($iteration = 0; $iteration < 3; $iteration++) {
            $response = Http::retry(3, 2000) // Retries up to 3 times, waiting 2000ms (2s) between attempts
                ->withHeaders([
                    'Authorization' => 'Bearer ' . $this->apiKey,
                    'Content-Type'  => 'application/json',
                ])
                ->post('https://api.groq.com/openai/v1/chat/completions', [
                    'model'    => $this->model,
                    'messages' => $messages,
                    'tools'    => $this->toolRegistry->getTools(),
                ]);

            if ($response->failed()) {
                Log::error('Groq API Error: ' . $response->body());
                return [
                    'role' => 'assistant',
                    'content' => 'An error occurred while communicating with the AI service: ' . $response->reason(),
                ];
            }

            $choice = $response->json('choices.0.message');

            if (!$choice) {
                return [
                    'role' => 'assistant',
                    'content' => 'Received empty response from AI engine.',
                ];
            }

            if (!empty($choice['tool_calls'])) {
                $messages[] = $choice;

                foreach ($choice['tool_calls'] as $toolCall) {
                    $toolName = $toolCall['function']['name'];
                    $toolArgs = json_decode($toolCall['function']['arguments'] ?? '{}', true) ?? [];

                    $toolOutput = $this->toolRegistry->execute($toolName, $toolArgs);

                    $messages[] = [
                        'role' => 'tool',
                        'tool_call_id' => $toolCall['id'],
                        'content' => json_encode($toolOutput),
                    ];
                }

                continue;
            }

            return [
                'role' => 'assistant',
                'content' => $choice['content'] ?? 'No text response generated.',
            ];
        }

        return [
            'role' => 'assistant',
            'content' => 'Execution limit reached without a final answer.',
        ];
    }
}