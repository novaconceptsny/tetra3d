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

    public function viewAny(User $user): void
    {
        //
    }

    public function view(User $user, Surface $surface): void
    {
        //
    }

    public function create(User $user): void
    {
        //
    }

    public function update(User $user, Surface $surface): void
    {
        //
    }

    public function delete(User $user, Surface $surface): void
    {
        //
    }

    public function restore(User $user, Surface $surface): void
    {
        //
    }

    public function forceDelete(User $user, Surface $surface): void
    {
        //
    }
}
