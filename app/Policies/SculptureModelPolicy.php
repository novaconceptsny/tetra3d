<?php

namespace App\Policies;

use App\Models\SculptureModel;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class SculptureModelPolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user)
    {
        // Only super admins can view sculptures
        return $user->isSuperAdmin();
    }

    public function view(User $user, SculptureModel $sculptureModel)
    {
        // Only super admins can view sculptures
        return $user->isSuperAdmin();
    }

    public function create(User $user)
    {
        // Only super admins can create sculptures
        return $user->isSuperAdmin();
    }

    public function update(User $user, SculptureModel $sculptureModel)
    {
        // Only super admins can update sculptures
        return $user->isSuperAdmin();
    }

    public function delete(User $user, SculptureModel $sculptureModel)
    {
        // Only super admins can delete sculptures
        return $user->isSuperAdmin();
    }

    public function restore(User $user, SculptureModel $sculptureModel)
    {
        // Only super admins can restore sculptures
        return $user->isSuperAdmin();
    }

    public function forceDelete(User $user, SculptureModel $sculptureModel)
    {
        // Only super admins can force delete sculptures
        return $user->isSuperAdmin();
    }
} 