<?php

namespace App\Services\Ai;

use App\Models\AiConversation;
use App\Models\AiMessage;
use App\Models\User;
use Illuminate\Support\Str;

class AiConversationService
{
    public function create(User $user, ?string $title = null): AiConversation
    {
        return $user->aiConversations()->create([
            'title' => $title ?: 'Nouvelle conversation',
        ]);
    }

    public function addUserMessage(AiConversation $conversation, string $content): AiMessage
    {
        return $conversation->messages()->create([
            'role' => 'user',
            'content' => $content,
        ]);
    }

    public function addAssistantMessage(AiConversation $conversation, string $content, ?array $toolCalls = null): AiMessage
    {
        return $conversation->messages()->create([
            'role' => 'assistant',
            'content' => $content,
            'tool_calls' => $toolCalls,
        ]);
    }

    /**
     * Return the conversation in the format expected by AiAgentService.
     * Only the latest 40 messages are sent to keep the LLM context bounded.
     */
    public function aiMessages(AiConversation $conversation, int $limit = 40): array
    {
        return $conversation->messages()
            ->whereIn('role', ['user', 'assistant'])
            ->latest('id')
            ->limit($limit)
            ->get()
            ->reverse()
            ->values()
            ->map(fn (AiMessage $message) => [
                'role' => $message->role,
                'content' => $message->content ?? '',
            ])
            ->all();
    }

    public function updateTitleFromFirstMessage(AiConversation $conversation, string $content): void
    {
        if ($conversation->title !== 'Nouvelle conversation') {
            return;
        }

        $title = preg_replace('/\s+/u', ' ', trim($content)) ?: 'Nouvelle conversation';
        $conversation->update([
            'title' => Str::limit($title, 60, '…'),
        ]);
    }
}
