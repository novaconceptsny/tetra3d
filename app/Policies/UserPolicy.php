<?php

namespace App\Policies;

use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class UserPolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user)
    {
        if ($user->isCompanyAdmin()) {
            return true;
        }
    }

    public function view(User $user, User $model)
    {
        if ($user->isCompanyAdmin()){
            return $user->company_id == $model->company_id;
        }
    }

    public function create(User $user)
    {
        if ($user->isCompanyAdmin()) {
            return true;
        }
    }

    public function update(User $user, User $model)
    {
        if ($user->isCompanyAdmin()){
            return $user->company_id == $model->company_id;
        }
    }

    public function delete(User $user, User $model)
    {
        if ($user->isCompanyAdmin()){
            return $user->company_id == $model->company_id;
        }
    }

    public function restore(User $user, User $model)
    {
        //
    }

    public function forceDelete(User $user, User $model)
    {
        //
    }
}
