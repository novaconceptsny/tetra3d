<?php

namespace App\Policies;

use App\Models\Spot;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class SpotPolicy
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

    public function view(User $user, Spot $spot): bool
    {
        //
    }

    public function create(User $user): bool
    {
        //
    }

    public function update(User $user, Spot $spot): bool
    {
        //
    }

    public function delete(User $user, Spot $spot): bool
    {
        //
    }

    public function restore(User $user, Spot $spot): bool
    {
        //
    }

    public function forceDelete(User $user, Spot $spot): bool
    {
        //
    }
}
