<?php

namespace App\Policies;

use App\Models\ArtworkCollection;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class ArtworkCollectionPolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user)
    {

    }

    public function view(User $user, ArtworkCollection $collection)
    {
    }

    public function create(User $user)
    {
    }

    public function update(
        User $user,
        ArtworkCollection $collection
    ) {
    }

    public function delete(
        User $user,
        ArtworkCollection $collection
    ) {
    }

    public function restore(
        User $user,
        ArtworkCollection $collection
    ) {
    }

    public function forceDelete(
        User $user,
        ArtworkCollection $collection
    ) {
    }
}
