<?php

namespace App\Policies;

use App\Models\Answer;
use App\Models\User;

class AnswerPolicy
{
    public function update(User $user, Answer $answer): bool
    {
        return $user->id === $answer->author_id;
    }

    public function delete(User $user, Answer $answer): bool
    {
        return $user->id === $answer->author_id;
    }
}