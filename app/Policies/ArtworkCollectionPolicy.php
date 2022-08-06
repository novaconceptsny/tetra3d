<?php

namespace App\Policies;

use App\Models\ArtworkCollection;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class ArtworkCollectionPolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user)
    {
        if ($user->isCompanyAdmin()) {
            return true;
        }
    }

    public function view(User $user, ArtworkCollection $collection)
    {
        if ($user->isCompanyAdmin()) {
            return $user->company_id == $collection->company_id;
        }
    }

    public function create(User $user)
    {
        if ($user->isCompanyAdmin()) {
            return true;
        }
    }

    public function update(User $user, ArtworkCollection $collection)
    {
        if ($user->isCompanyAdmin()) {
            return $user->company_id == $collection->company_id;
        }
    }

    public function delete(User $user, ArtworkCollection $collection)
    {
        if ($user->isCompanyAdmin()) {
            return $user->company_id == $collection->company_id;
        }
    }

    public function restore(User $user, ArtworkCollection $collection)
    {
    }

    public function forceDelete(User $user, ArtworkCollection $collection)
    {
    }
}
