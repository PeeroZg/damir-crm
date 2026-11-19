<?php

namespace App\Policies;

use App\Models\Deal;
use App\Models\User;

/**
 * Everyone sees the whole pipeline, but a deal is changed only by its owner or an admin.
 */
class DealPolicy
{
    public function viewAny(User $user): bool
    {
        return true;
    }

    public function view(User $user, Deal $deal): bool
    {
        return true;
    }

    public function create(User $user): bool
    {
        return true;
    }

    public function update(User $user, Deal $deal): bool
    {
        return $user->isAdmin() || $deal->owner_id === $user->id;
    }

    public function delete(User $user, Deal $deal): bool
    {
        return $this->update($user, $deal);
    }

    /** Re-assigning a deal to another sales rep is an admin decision. */
    public function reassign(User $user): bool
    {
        return $user->isAdmin();
    }
}
