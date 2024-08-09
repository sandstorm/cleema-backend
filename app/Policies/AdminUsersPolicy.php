<?php

namespace App\Policies;

use App\Models\AdminUsers;

use Illuminate\Auth\Access\HandlesAuthorization;

class AdminUsersPolicy
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
        return $adminUsers->can('view_any_admin::users');
    }

    /**
     * Determine whether the adminUsers can view the model.
     *
     * @param  \App\Models\AdminUsers  $adminUsers
     * @return bool
     */
    public function view(AdminUsers $adminUsers): bool
    {
        return $adminUsers->can('view_admin::users');
    }

    /**
     * Determine whether the adminUsers can create models.
     *
     * @param  \App\Models\AdminUsers  $adminUsers
     * @return bool
     */
    public function create(AdminUsers $adminUsers): bool
    {
        return $adminUsers->can('create_admin::users');
    }

    /**
     * Determine whether the adminUsers can update the model.
     *
     * @param  \App\Models\AdminUsers  $adminUsers
     * @return bool
     */
    public function update(AdminUsers $adminUsers, AdminUsers $model): bool
    {
        if($model->id == $adminUsers->id || $adminUsers->hasRole('super_admin')) {
            return true;
        }
        return false;
    }

    /**
     * Determine whether the adminUsers can delete the model.
     *
     * @param  \App\Models\AdminUsers  $adminUsers
     * @return bool
     */
    public function delete(AdminUsers $adminUsers): bool
    {
        return $adminUsers->can('delete_admin::users');
    }

    /**
     * Determine whether the adminUsers can bulk delete.
     *
     * @param  \App\Models\AdminUsers  $adminUsers
     * @return bool
     */
    public function deleteAny(AdminUsers $adminUsers): bool
    {
        return $adminUsers->can('delete_any_admin::users');
    }

    /**
     * Determine whether the adminUsers can permanently delete.
     *
     * @param  \App\Models\AdminUsers  $adminUsers
     * @return bool
     */
    public function forceDelete(AdminUsers $adminUsers): bool
    {
        return $adminUsers->can('force_delete_admin::users');
    }

    /**
     * Determine whether the adminUsers can permanently bulk delete.
     *
     * @param  \App\Models\AdminUsers  $adminUsers
     * @return bool
     */
    public function forceDeleteAny(AdminUsers $adminUsers): bool
    {
        return $adminUsers->can('force_delete_any_admin::users');
    }

    /**
     * Determine whether the adminUsers can restore.
     *
     * @param  \App\Models\AdminUsers  $adminUsers
     * @return bool
     */
    public function restore(AdminUsers $adminUsers): bool
    {
        return $adminUsers->can('restore_admin::users');
    }

    /**
     * Determine whether the adminUsers can bulk restore.
     *
     * @param  \App\Models\AdminUsers  $adminUsers
     * @return bool
     */
    public function restoreAny(AdminUsers $adminUsers): bool
    {
        return $adminUsers->can('restore_any_admin::users');
    }

    /**
     * Determine whether the adminUsers can bulk restore.
     *
     * @param  \App\Models\AdminUsers  $adminUsers
     * @return bool
     */
    public function replicate(AdminUsers $adminUsers): bool
    {
        return $adminUsers->can('replicate_admin::users');
    }

    /**
     * Determine whether the adminUsers can reorder.
     *
     * @param  \App\Models\AdminUsers  $adminUsers
     * @return bool
     */
    public function reorder(AdminUsers $adminUsers): bool
    {
        return $adminUsers->can('reorder_admin::users');
    }
}
