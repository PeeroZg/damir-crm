<?php

namespace App\Policies;

use App\Models\Deal;
use App\Models\Offer;
use App\Models\User;

class OfferPolicy
{
    public function viewAny(User $user): bool
    {
        return true;
    }

    public function view(User $user, Offer $offer): bool
    {
        return true;
    }

    /** Offers are prepared by whoever owns the deal. */
    public function create(User $user, ?Deal $deal = null): bool
    {
        return $deal === null || $user->isAdmin() || $deal->owner_id === $user->id;
    }

    public function update(User $user, Offer $offer): bool
    {
        return $this->manage($user, $offer) && $offer->isEditable();
    }

    public function changeStatus(User $user, Offer $offer): bool
    {
        return $this->manage($user, $offer);
    }

    public function revise(User $user, Offer $offer): bool
    {
        return $this->manage($user, $offer);
    }

    public function delete(User $user, Offer $offer): bool
    {
        return $this->manage($user, $offer);
    }

    private function manage(User $user, Offer $offer): bool
    {
        return $user->isAdmin() || $offer->owner_id === $user->id;
    }
}
