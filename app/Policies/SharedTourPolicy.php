<?php

namespace App\Policies;

use App\Models\SharedTour;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class SharedTourPolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user): bool
    {

    }

    public function view(User $user, SharedTour $sharedTour): bool
    {
    }

    public function create(User $user): bool
    {
    }

    public function update(User $user, SharedTour $sharedTour): bool
    {
    }

    public function delete(User $user, SharedTour $sharedTour): bool
    {
    }

    public function restore(User $user, SharedTour $sharedTour): bool
    {
    }

    public function forceDelete(User $user, SharedTour $sharedTour): bool
    {
    }
}
