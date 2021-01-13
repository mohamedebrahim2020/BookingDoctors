<?php

namespace App\Policies;

use App\Models\Admin;
use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Auth\Access\Response;

class AdminPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any models.
     *
     * @param  \App\Models\Admin  $admin
     * @return mixed
     */
    public function viewAny(Admin $admin)
    {
        //
    }

    /**
     * Determine whether the user can view the model.
     *
     * @param  \App\Models\Admin  $admin
     * @param  \App\Models\Admin  $admin
     * @return mixed
     */
    // public function view(Admin $admin, Admin $admin)
    // {
    //     //
    // }

    /**
     * Determine whether the user can create models.
     *
     * @param  \App\Models\Admin  $admin
     * @return mixed
     */
    public function create(Admin $superAdmin)
    {
        return $superAdmin->is_super == 1
        ? Response::allow()
        : Response::deny('You do not authorized to create admin', 403);
    }

    /**
     * Determine whether the user can update the model.
     *
     * @param  \App\Models\Admin  $admin
     * @param  \App\Models\Admin  $admin
     * @return mixed
     */
    public function update(Admin $superAdmin, Admin $admin)
    {
        return $superAdmin->is_super && !$admin->is_super
        ? Response::allow()
        : Response::deny('You do not authorized to update admin', 403);
    }

    /**
     * Determine whether the user can delete the model.
     *
     * @param  \App\Models\Admin  $admin
     * @param  \App\Models\Admin  $admin
     * @return mixed
     */
    public function delete(Admin $superAdmin, Admin $admin)
    {
        return $superAdmin->is_super && !$admin->is_super
        ? Response::allow()
        : Response::deny('only super admin can delete admin and not be deleted', 403);
    }

    /**
     * Determine whether the user can restore the model.
     *
     * @param  \App\Models\Admin  $admin
     * @param  \App\Models\Admin  $admin
     * @return mixed
     */
    // public function restore(Admin $admin, Admin $admin)
    // {
    //     //
    // }

    /**
     * Determine whether the user can permanently delete the model.
     *
     * @param  \App\Models\Admin  $admin
     * @param  \App\Models\Admin  $admin
     * @return mixed
     */
    // public function forceDelete(Admin $admin, Admin $admin)
    // {
    //     //
    // }
}
