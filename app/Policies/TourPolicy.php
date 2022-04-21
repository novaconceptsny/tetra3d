<?php

namespace App\Policies;

use App\Models\Tour;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class TourPolicy
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

    public function view(User $user, Tour $tour)
    {
        //
    }

    public function create(User $user)
    {
        //
    }

    public function update(User $user, Tour $tour)
    {
        //
    }

    public function delete(User $user, Tour $tour)
    {
        //
    }

    public function restore(User $user, Tour $tour)
    {
        //
    }

    public function forceDelete(User $user, Tour $tour)
    {
        //
    }
}
