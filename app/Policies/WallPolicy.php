<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Wall;
use Illuminate\Auth\Access\HandlesAuthorization;

class WallPolicy
{
    use HandlesAuthorization;

    public function __construct()
    {
        //
    }

    public function viewAny(User $user): bool
    {
        //
    }

    public function view(User $user, Wall $wall): bool
    {
        //
    }

    public function create(User $user): bool
    {
        //
    }

    public function update(User $user, Wall $wall): bool
    {
        //
    }

    public function delete(User $user, Wall $wall): bool
    {
        //
    }

    public function restore(User $user, Wall $wall): bool
    {
        //
    }

    public function forceDelete(User $user, Wall $wall): bool
    {
        //
    }
}
