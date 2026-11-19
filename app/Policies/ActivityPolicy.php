<?php

namespace App\Policies;

use App\Models\Activity;
use App\Models\User;

class ActivityPolicy
{
    public function update(User $user, Activity $activity): bool
    {
        return $user->isAdmin()
            || $activity->user_id === $user->id
            || $activity->assigned_to === $user->id;
    }

    public function delete(User $user, Activity $activity): bool
    {
        return $user->isAdmin() || $activity->user_id === $user->id;
    }
}
