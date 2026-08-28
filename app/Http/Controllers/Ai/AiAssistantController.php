<?php

namespace App\Http\Controllers\Ai;

use App\Http\Controllers\Controller;
use App\Http\Requests\SendAiMessageRequest;
use App\Models\AiConversation;
use App\Models\Customer;
use Illuminate\Support\Facades\Gate;
use App\Models\Product;
use App\Services\Ai\AiConversationService;
use App\Services\Ai\Services\AiAgentService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AiAssistantController extends Controller
{
    public function __construct(
        protected AiAgentService $agentService,
        protected AiConversationService $conversationService,
    ) {
    }

    public function index(): View|RedirectResponse
    {
        $user = auth()->user();

        $conversations = $user->aiConversations()
            ->withCount('messages')
            ->latest('updated_at')
            ->get();

        $conversation = $conversations->first();

        if (!$conversation) {
            $conversation = $this->conversationService->create($user);
            $conversations = collect([$conversation]);
        }

        return $this->render($conversation, $conversations);
    }

    public function store(): JsonResponse
    {
        $conversation = $this->conversationService->create(auth()->user());

        return response()->json([
            'id' => $conversation->id,
            'title' => $conversation->title,
            'url' => route('ai.conversations.show', $conversation),
        ], 201);
    }

    public function show(AiConversation $conversation): View
    {
        Gate::authorize('view', $conversation);

        $conversations = auth()->user()->aiConversations()
            ->withCount('messages')
            ->latest('updated_at')
            ->get();

        return $this->render($conversation, $conversations);
    }

    public function send(SendAiMessageRequest $request, AiConversation $conversation): JsonResponse
    {
        Gate::authorize('view', $conversation);

        $userMessage = $request->validated('message');
        $this->conversationService->addUserMessage($conversation, $userMessage);
        $this->conversationService->updateTitleFromFirstMessage($conversation, $userMessage);

        try {
            $messages = $this->conversationService->aiMessages($conversation);
            $response = $this->agentService->chat($messages);
            $aiContent = trim((string) ($response['content'] ?? 'Une erreur est survenue.'));

            $assistantMessage = $this->conversationService->addAssistantMessage(
                $conversation,
                $aiContent,
                $response['tool_calls'] ?? null,
            );

            $conversation->refresh();

            return response()->json([
                'role' => 'assistant',
                'content' => $assistantMessage->content,
                'conversation' => [
                    'id' => $conversation->id,
                    'title' => $conversation->title,
                ],
            ]);
        } catch (\Throwable $e) {
            report($e);

            return response()->json([
                'message' => 'Impossible de contacter le service IA. Votre message a été conservé dans la conversation.',
            ], 502);
        }
    }

    public function update(Request $request, AiConversation $conversation): JsonResponse
    {
        Gate::authorize('update', $conversation);

        $validated = $request->validate([
            'title' => ['required', 'string', 'max:60'],
        ]);

        $conversation->update([
            'title' => trim($validated['title']),
        ]);

        return response()->json([
            'id' => $conversation->id,
            'title' => $conversation->title,
        ]);
    }

    public function destroy(AiConversation $conversation): JsonResponse
    {
        Gate::authorize('delete', $conversation);

        $conversation->delete();

        $nextConversation = auth()->user()->aiConversations()
            ->latest('updated_at')
            ->first();

        return response()->json([
            'status' => 'success',
            'redirect' => $nextConversation
                ? route('ai.conversations.show', $nextConversation)
                : route('ai.index'),
        ]);
    }

    protected function render(AiConversation $conversation, $conversations): View
    {
        $messages = $conversation->messages()
            ->whereIn('role', ['user', 'assistant'])
            ->oldest('id')
            ->get();

        $alerts = [
            'low_stock_count' => Product::whereColumn('stock', '<=', 'alert_threshold')->count(),
            'high_debt_customers' => Customer::all()->filter(fn ($customer) => $customer->total_outstanding_debt > 0)->count(),
        ];

        return view('ai.index', compact('alerts', 'conversations', 'conversation', 'messages'));
    }
}
