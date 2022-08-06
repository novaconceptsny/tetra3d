<?php

namespace App\Policies;

use App\Models\Project;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class ProjectPolicy
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

    public function view(User $user, Project $project)
    {
        if ($user->isCompanyAdmin()) {
            return $user->company_id == $project->company_id;
        }
    }

    public function create(User $user)
    {
        if ($user->isCompanyAdmin()) {
            return true;
        }
    }

    public function update(User $user, Project $project)
    {
        if ($user->isCompanyAdmin()) {
            return $user->company_id == $project->company_id;
        }
    }

    public function delete(User $user, Project $project)
    {
        if ($user->isCompanyAdmin()) {
            return $user->company_id == $project->company_id;
        }
    }

    public function restore(User $user, Project $project)
    {
        //
    }

    public function forceDelete(User $user, Project $project)
    {
        //
    }
}
