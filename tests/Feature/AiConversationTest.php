<?php

use App\Models\AiConversation;
use App\Models\AiMessage;
use App\Models\User;
use App\Services\Ai\Services\AiAgentService;

it('creates an AI conversation for the authenticated user', function () {
    $user = User::factory()->create();

    $response = $this->actingAs($user)->postJson(route('ai.conversations.store'));

    $response->assertCreated()
        ->assertJsonPath('title', 'Nouvelle conversation');

    expect(AiConversation::where('user_id', $user->id)->count())->toBe(1);
});

it('only allows a user to view their own conversations', function () {
    $owner = User::factory()->create();
    $otherUser = User::factory()->create();
    $conversation = AiConversation::create([
        'user_id' => $owner->id,
        'title' => 'Privée',
    ]);

    $this->actingAs($otherUser)
        ->get(route('ai.conversations.show', $conversation))
        ->assertForbidden();
});

it('stores both the user message and the assistant response', function () {
    $user = User::factory()->create();
    $conversation = AiConversation::create([
        'user_id' => $user->id,
        'title' => 'Nouvelle conversation',
    ]);

    $agent = Mockery::mock(AiAgentService::class);
    $agent->shouldReceive('chat')
        ->once()
        ->with(Mockery::on(function (array $messages) {
            return $messages === [
                ['role' => 'user', 'content' => 'Quels produits sont en rupture ?'],
            ];
        }))
        ->andReturn([
            'role' => 'assistant',
            'content' => 'Voici les produits en rupture.',
        ]);

    $this->app->instance(AiAgentService::class, $agent);

    $response = $this->actingAs($user)->postJson(
        route('ai.conversations.messages.store', $conversation),
        ['message' => 'Quels produits sont en rupture ?']
    );

    $response->assertOk()
        ->assertJsonPath('content', 'Voici les produits en rupture.')
        ->assertJsonPath('conversation.title', 'Quels produits sont en rupture ?');

    expect($conversation->fresh()->messages)->toHaveCount(2);
    expect(AiMessage::where('conversation_id', $conversation->id)->where('role', 'user')->value('content'))
        ->toBe('Quels produits sont en rupture ?');
    expect(AiMessage::where('conversation_id', $conversation->id)->where('role', 'assistant')->value('content'))
        ->toBe('Voici les produits en rupture.');
});

it('deletes only the authenticated user conversation and its messages', function () {
    $user = User::factory()->create();
    $otherUser = User::factory()->create();

    $conversation = AiConversation::create(['user_id' => $user->id, 'title' => 'À supprimer']);
    $otherConversation = AiConversation::create(['user_id' => $otherUser->id, 'title' => 'À conserver']);

    $conversation->messages()->create(['role' => 'user', 'content' => 'Bonjour']);

    $response = $this->actingAs($user)
        ->deleteJson(route('ai.conversations.destroy', $conversation));

    $response->assertOk();

    expect(AiConversation::find($conversation->id))->toBeNull();
    expect(AiMessage::where('conversation_id', $conversation->id)->count())->toBe(0);
    expect(AiConversation::find($otherConversation->id))->not->toBeNull();
});
