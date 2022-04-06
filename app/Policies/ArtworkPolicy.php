<?php

namespace App\Policies;

use App\Models\Artwork;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class ArtworkPolicy
{
    use HandlesAuthorization;

    public function __construct()
    {
        //
    }

    public function viewAny(User $user)
    {
        //
    }

    public function view(User $user, Artwork $artwork)
    {
        //
    }

    public function create(User $user)
    {
        //
    }

    public function update(User $user, Artwork $artwork)
    {
        //
    }

    public function delete(User $user, Artwork $artwork)
    {
        //
    }

    public function restore(User $user, Artwork $artwork)
    {
        //
    }

    public function forceDelete(User $user, Artwork $artwork)
    {
        //
    }
}
