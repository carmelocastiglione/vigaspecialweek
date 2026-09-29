<?php

namespace App\Policies;

use App\Models\User;
use App\Models\SchoolClass;
use Illuminate\Auth\Access\Response;

class SchoolClassPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return false;
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, SchoolClass $model): bool
    {
        return $user->can('school-classes.view');
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->can('school-classes.create');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, SchoolClass $model): bool
    {
        return $user->can('school-classes.edit');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, SchoolClass $model): bool
    {
        return $user->can('school-classes.delete');
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, SchoolClass $model): bool
    {
        return $user->can('school-classes.restore');
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, SchoolClass $model): bool
    {
        return $user->can('school-classes.forceDelete');
    }

}
