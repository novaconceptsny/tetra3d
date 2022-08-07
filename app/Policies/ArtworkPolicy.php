<?php

namespace App\Policies;

use App\Models\Artwork;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class ArtworkPolicy
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

    public function view(User $user, Artwork $artwork)
    {
        if ($user->isCompanyAdmin()) {
            return true;
        }
    }

    public function create(User $user)
    {
        return $user->isCompanyAdmin();
    }

    public function update(User $user, Artwork $artwork)
    {
        if ($user->isCompanyAdmin()){
            return $user->company_id == $artwork->company_id;
        }
    }

    public function delete(User $user, Artwork $artwork)
    {
        if ($user->isCompanyAdmin()){
            return $user->company_id == $artwork->company_id;
        }
    }

    public function restore(User $user, Artwork $artwork)
    {
        //
    }

    public function forceDelete(User $user, Artwork $artwork)
    {
        //
    }
}
