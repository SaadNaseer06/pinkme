<?php

namespace App\Policies;

use App\Models\User;

class UserPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->role_id === 1; // Only Admin
    }

    public function view(User $user, User $model): bool
    {
        return $user->id === $model->id || $user->role_id === 1;
    }

    public function create(User $user): bool
    {
        return $user->role_id === 1;
    }

    public function update(User $user, User $model): bool
    {
        return $user->id === $model->id || $user->role_id === 1;
    }

    public function delete(User $user, User $model): bool
    {
        return $user->role_id === 1;
    }
}
