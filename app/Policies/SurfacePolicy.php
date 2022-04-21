<?php

namespace App\Policies;

use App\Models\Surface;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class SurfacePolicy
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

    public function view(User $user, Surface $surface): bool
    {
        //
    }

    public function create(User $user): bool
    {
        //
    }

    public function update(User $user, Surface $surface): bool
    {
        //
    }

    public function delete(User $user, Surface $surface): bool
    {
        //
    }

    public function restore(User $user, Surface $surface): bool
    {
        //
    }

    public function forceDelete(User $user, Surface $surface): bool
    {
        //
    }
}
