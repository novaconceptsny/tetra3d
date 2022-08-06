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

    public function viewAny(User $user)
    {
        if ($user->isCompanyAdmin()) {
            return true;
        }
    }

    public function view(User $user, Spot $spot)
    {
        if ($user->isCompanyAdmin()) {
            return $user->company_id == $spot->company_id;
        }
    }

    public function create(User $user)
    {
        if ($user->isCompanyAdmin()) {
            return true;
        }
    }

    public function update(User $user, Spot $spot)
    {
        if ($user->isCompanyAdmin()) {
            return $user->company_id == $spot->company_id;
        }
    }

    public function delete(User $user, Spot $spot)
    {
        if ($user->isCompanyAdmin()) {
            return $user->company_id == $spot->company_id;
        }
    }

    public function restore(User $user, Spot $spot)
    {
        //
    }

    public function forceDelete(User $user, Spot $spot)
    {
        //
    }
}
