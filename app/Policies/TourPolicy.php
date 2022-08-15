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
        /*if ($user->isCompanyAdmin()) {
            return true;
        }*/
    }

    public function view(User $user, Tour $tour)
    {
        /*if ($user->isCompanyAdmin()) {
            return $user->company_id == $tour->company_id;
        }*/
    }

    public function create(User $user)
    {
        /*if ($user->isCompanyAdmin()) {
            return true;
        }*/
    }

    public function update(User $user, Tour $tour)
    {
        /*if ($user->isCompanyAdmin()) {
            return $user->company_id == $tour->company_id;
        }*/
    }

    public function delete(User $user, Tour $tour)
    {
        /*if ($user->isCompanyAdmin()) {
            return $user->company_id == $tour->company_id;
        }*/
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
