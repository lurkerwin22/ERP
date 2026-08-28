<?php

namespace App\Policies;

use App\Models\AiConversation;
use App\Models\User;

class AiConversationPolicy
{
    public function view(User $user, AiConversation $conversation): bool
    {
        return $conversation->user_id === $user->id;
    }

    public function update(User $user, AiConversation $conversation): bool
    {
        return $conversation->user_id === $user->id;
    }

    public function delete(User $user, AiConversation $conversation): bool
    {
        return $conversation->user_id === $user->id;
    }
}
