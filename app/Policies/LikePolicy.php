<?php

namespace App\Policies;

use App\Models\Like;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class LikePolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user)
    {

    }

    public function view(User $user, Like $like)
    {
    }

    public function create(User $user)
    {
    }

    public function update(User $user, Like $like)
    {
    }

    public function delete(User $user, Like $like)
    {
        return $like->user_id == $user->id;
    }

    public function restore(User $user, Like $like)
    {
    }

    public function forceDelete(User $user, Like $like)
    {
    }
}
