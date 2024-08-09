<?php

namespace App\Policies;

use App\Models\AdminUsers;
use Spatie\Permission\Models\Role;
use Illuminate\Auth\Access\HandlesAuthorization;

class RolePolicy
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
        return $adminUsers->can('view_any_role');
    }

    /**
     * Determine whether the adminUsers can view the model.
     *
     * @param  \App\Models\AdminUsers  $adminUsers
     * @param  \Spatie\Permission\Models\Role  $role
     * @return bool
     */
    public function view(AdminUsers $adminUsers, Role $role): bool
    {
        return $adminUsers->can('view_role');
    }

    /**
     * Determine whether the adminUsers can create models.
     *
     * @param  \App\Models\AdminUsers  $adminUsers
     * @return bool
     */
    public function create(AdminUsers $adminUsers): bool
    {
        return false;
        //return $adminUsers->can('create_role');
    }

    /**
     * Determine whether the adminUsers can update the model.
     *
     * @param  \App\Models\AdminUsers  $adminUsers
     * @param  \Spatie\Permission\Models\Role  $role
     * @return bool
     */
    public function update(AdminUsers $adminUsers, Role $role): bool
    {
        return false;
        //return $adminUsers->can('update_role');
    }

    /**
     * Determine whether the adminUsers can delete the model.
     *
     * @param  \App\Models\AdminUsers  $adminUsers
     * @param  \Spatie\Permission\Models\Role  $role
     * @return bool
     */
    public function delete(AdminUsers $adminUsers, Role $role): bool
    {
        return false;
        //return $adminUsers->can('delete_role');
    }

    /**
     * Determine whether the adminUsers can bulk delete.
     *
     * @param  \App\Models\AdminUsers  $adminUsers
     * @return bool
     */
    public function deleteAny(AdminUsers $adminUsers): bool
    {
        //return $adminUsers->can('delete_any_role');
        return false;
    }

    /**
     * Determine whether the adminUsers can permanently delete.
     *
     * @param  \App\Models\AdminUsers  $adminUsers
     * @param  \Spatie\Permission\Models\Role  $role
     * @return bool
     */
    public function forceDelete(AdminUsers $adminUsers, Role $role): bool
    {
        //return $adminUsers->can('{{ ForceDelete }}');#
        return false;
    }

    /**
     * Determine whether the adminUsers can permanently bulk delete.
     *
     * @param  \App\Models\AdminUsers  $adminUsers
     * @return bool
     */
    public function forceDeleteAny(AdminUsers $adminUsers): bool
    {
        //return $adminUsers->can('{{ ForceDeleteAny }}');
        return false;
    }

    /**
     * Determine whether the adminUsers can restore.
     *
     * @param  \App\Models\AdminUsers  $adminUsers
     * @param  \Spatie\Permission\Models\Role  $role
     * @return bool
     */
    public function restore(AdminUsers $adminUsers, Role $role): bool
    {
        //return $adminUsers->can('{{ Restore }}');
        return false;
    }

    /**
     * Determine whether the adminUsers can bulk restore.
     *
     * @param  \App\Models\AdminUsers  $adminUsers
     * @return bool
     */
    public function restoreAny(AdminUsers $adminUsers): bool
    {
        //return $adminUsers->can('{{ RestoreAny }}');
        return false;
    }

    /**
     * Determine whether the adminUsers can replicate.
     *
     * @param  \App\Models\AdminUsers  $adminUsers
     * @param  \Spatie\Permission\Models\Role  $role
     * @return bool
     */
    public function replicate(AdminUsers $adminUsers, Role $role): bool
    {
        //return $adminUsers->can('{{ Replicate }}');
        return false;
    }

    /**
     * Determine whether the adminUsers can reorder.
     *
     * @param  \App\Models\AdminUsers  $adminUsers
     * @return bool
     */
    public function reorder(AdminUsers $adminUsers): bool
    {
        //return $adminUsers->can('{{ Reorder }}');
        return false;
    }

}
