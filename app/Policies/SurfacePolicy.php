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

    public function viewAny(User $user)
    {
        if ($user->isCompanyAdmin()) {
            return true;
        }
    }

    public function view(User $user, Surface $surface)
    {
        //
    }

    public function create(User $user)
    {
        //
    }

    public function update(User $user, Surface $surface)
    {
        //
    }

    public function delete(User $user, Surface $surface)
    {
        //
    }

    public function restore(User $user, Surface $surface)
    {
        //
    }

    public function forceDelete(User $user, Surface $surface)
    {
        //
    }
}
