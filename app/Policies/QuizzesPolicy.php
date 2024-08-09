<?php

namespace App\Policies;

use App\Models\AdminUsers;
use App\Models\Quizzes;
use Illuminate\Auth\Access\HandlesAuthorization;

class QuizzesPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the adminUsers can view any models.
     *
     * @param  \App\Models\AdminUsers  $adminUsers
     * @return bool
     */
    public function viewAny(AdminUsers $adminUsers): bool
    {
        return $adminUsers->can('view_any_quizzes');
    }

    /**
     * Determine whether the adminUsers can view the model.
     *
     * @param  \App\Models\AdminUsers  $adminUsers
     * @param  \App\Models\Quizzes  $quizzes
     * @return bool
     */
    public function view(AdminUsers $adminUsers, Quizzes $quizzes): bool
    {
        return $adminUsers->can('view_quizzes');
    }

    /**
     * Determine whether the adminUsers can create models.
     *
     * @param  \App\Models\AdminUsers  $adminUsers
     * @return bool
     */
    public function create(AdminUsers $adminUsers): bool
    {
        return $adminUsers->can('create_quizzes');
    }

    /**
     * Determine whether the adminUsers can update the model.
     *
     * @param  \App\Models\AdminUsers  $adminUsers
     * @param  \App\Models\Quizzes  $quizzes
     * @return bool
     */
    public function update(AdminUsers $adminUsers, Quizzes $quizzes): bool
    {
        if($quizzes->created_by_id == $adminUsers->id || $adminUsers->hasRole(['super_admin', 'editor'])) {
            return true;
        }
        return false;
    }

    /**
     * Determine whether the adminUsers can delete the model.
     *
     * @param  \App\Models\AdminUsers  $adminUsers
     * @param  \App\Models\Quizzes  $quizzes
     * @return bool
     */
    public function delete(AdminUsers $adminUsers, Quizzes $quizzes): bool
    {
        if($quizzes->created_by_id == $adminUsers->id || $adminUsers->hasRole(['super_admin', 'editor'])) {
            return true;
        }
        return false;
    }

    /**
     * Determine whether the adminUsers can bulk delete.
     *
     * @param  \App\Models\AdminUsers  $adminUsers
     * @return bool
     */
    public function deleteAny(AdminUsers $adminUsers): bool
    {
        return $adminUsers->can('delete_any_quizzes');
    }

    /**
     * Determine whether the adminUsers can permanently delete.
     *
     * @param  \App\Models\AdminUsers  $adminUsers
     * @param  \App\Models\Quizzes  $quizzes
     * @return bool
     */
    public function forceDelete(AdminUsers $adminUsers, Quizzes $quizzes): bool
    {
        return $adminUsers->can('force_delete_quizzes');
    }

    /**
     * Determine whether the adminUsers can permanently bulk delete.
     *
     * @param  \App\Models\AdminUsers  $adminUsers
     * @return bool
     */
    public function forceDeleteAny(AdminUsers $adminUsers): bool
    {
        return $adminUsers->can('force_delete_any_quizzes');
    }

    /**
     * Determine whether the adminUsers can restore.
     *
     * @param  \App\Models\AdminUsers  $adminUsers
     * @param  \App\Models\Quizzes  $quizzes
     * @return bool
     */
    public function restore(AdminUsers $adminUsers, Quizzes $quizzes): bool
    {
        return $adminUsers->can('restore_quizzes');
    }

    /**
     * Determine whether the adminUsers can bulk restore.
     *
     * @param  \App\Models\AdminUsers  $adminUsers
     * @return bool
     */
    public function restoreAny(AdminUsers $adminUsers): bool
    {
        return $adminUsers->can('restore_any_quizzes');
    }

    /**
     * Determine whether the adminUsers can replicate.
     *
     * @param  \App\Models\AdminUsers  $adminUsers
     * @param  \App\Models\Quizzes  $quizzes
     * @return bool
     */
    public function replicate(AdminUsers $adminUsers, Quizzes $quizzes): bool
    {
        return $adminUsers->can('replicate_quizzes');
    }

    /**
     * Determine whether the adminUsers can reorder.
     *
     * @param  \App\Models\AdminUsers  $adminUsers
     * @return bool
     */
    public function reorder(AdminUsers $adminUsers): bool
    {
        return $adminUsers->can('reorder_quizzes');
    }

}