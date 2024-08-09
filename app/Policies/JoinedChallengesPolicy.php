<?php

namespace App\Policies;

use App\Models\AdminUsers;
use App\Models\JoinedChallenges;
use Illuminate\Auth\Access\HandlesAuthorization;

class JoinedChallengesPolicy
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
        return $adminUsers->can('view_any_joined::challenges');
    }

    /**
     * Determine whether the adminUsers can view the model.
     *
     * @param  \App\Models\AdminUsers  $adminUsers
     * @param  \App\Models\JoinedChallenges  $joinedChallenges
     * @return bool
     */
    public function view(AdminUsers $adminUsers, JoinedChallenges $joinedChallenges): bool
    {
        return $adminUsers->can('view_joined::challenges');
    }

    /**
     * Determine whether the adminUsers can create models.
     *
     * @param  \App\Models\AdminUsers  $adminUsers
     * @return bool
     */
    public function create(AdminUsers $adminUsers): bool
    {
        return $adminUsers->can('create_joined::challenges');
    }

    /**
     * Determine whether the adminUsers can update the model.
     *
     * @param  \App\Models\AdminUsers  $adminUsers
     * @param  \App\Models\JoinedChallenges  $joinedChallenges
     * @return bool
     */
    public function update(AdminUsers $adminUsers, JoinedChallenges $joinedChallenges): bool
    {
        return $adminUsers->can('update_joined::challenges');
    }

    /**
     * Determine whether the adminUsers can delete the model.
     *
     * @param  \App\Models\AdminUsers  $adminUsers
     * @param  \App\Models\JoinedChallenges  $joinedChallenges
     * @return bool
     */
    public function delete(AdminUsers $adminUsers, JoinedChallenges $joinedChallenges): bool
    {
        return $adminUsers->can('delete_joined::challenges');
    }

    /**
     * Determine whether the adminUsers can bulk delete.
     *
     * @param  \App\Models\AdminUsers  $adminUsers
     * @return bool
     */
    public function deleteAny(AdminUsers $adminUsers): bool
    {
        return $adminUsers->can('delete_any_joined::challenges');
    }

    /**
     * Determine whether the adminUsers can permanently delete.
     *
     * @param  \App\Models\AdminUsers  $adminUsers
     * @param  \App\Models\JoinedChallenges  $joinedChallenges
     * @return bool
     */
    public function forceDelete(AdminUsers $adminUsers, JoinedChallenges $joinedChallenges): bool
    {
        return $adminUsers->can('force_delete_joined::challenges');
    }

    /**
     * Determine whether the adminUsers can permanently bulk delete.
     *
     * @param  \App\Models\AdminUsers  $adminUsers
     * @return bool
     */
    public function forceDeleteAny(AdminUsers $adminUsers): bool
    {
        return $adminUsers->can('force_delete_any_joined::challenges');
    }

    /**
     * Determine whether the adminUsers can restore.
     *
     * @param  \App\Models\AdminUsers  $adminUsers
     * @param  \App\Models\JoinedChallenges  $joinedChallenges
     * @return bool
     */
    public function restore(AdminUsers $adminUsers, JoinedChallenges $joinedChallenges): bool
    {
        return $adminUsers->can('restore_joined::challenges');
    }

    /**
     * Determine whether the adminUsers can bulk restore.
     *
     * @param  \App\Models\AdminUsers  $adminUsers
     * @return bool
     */
    public function restoreAny(AdminUsers $adminUsers): bool
    {
        return $adminUsers->can('restore_any_joined::challenges');
    }

    /**
     * Determine whether the adminUsers can replicate.
     *
     * @param  \App\Models\AdminUsers  $adminUsers
     * @param  \App\Models\JoinedChallenges  $joinedChallenges
     * @return bool
     */
    public function replicate(AdminUsers $adminUsers, JoinedChallenges $joinedChallenges): bool
    {
        return $adminUsers->can('replicate_joined::challenges');
    }

    /**
     * Determine whether the adminUsers can reorder.
     *
     * @param  \App\Models\AdminUsers  $adminUsers
     * @return bool
     */
    public function reorder(AdminUsers $adminUsers): bool
    {
        return $adminUsers->can('reorder_joined::challenges');
    }

}
