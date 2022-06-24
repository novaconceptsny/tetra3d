<?php

namespace App\Policies;

use App\Models\Map;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class MapPolicy
{
    use HandlesAuthorization;

    public function __construct()
    {
        //
    }

    public function viewAny(User $user): void
    {
        //
    }

    public function view(User $user, Map $map): void
    {
        //
    }

    public function create(User $user): void
    {
        //
    }

    public function update(User $user, Map $map): void
    {
        //
    }

    public function delete(User $user, Map $map): void
    {
        //
    }

    public function restore(User $user, Map $map): void
    {
        //
    }

    public function forceDelete(User $user, Map $map): void
    {
        //
    }
}
