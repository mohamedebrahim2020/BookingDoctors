<?php

namespace App\Policies;

use App\Models\Admin;
use App\Models\Doctor;
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

    /*
     * Determine whether the user can create models.
     *
     * @param  \App\Models\Admin  $admin
     * @return mixed
     */
    public function create(Admin $superAdmin)
    {
        return $superAdmin->is_super;
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
        return $superAdmin->is_super && !$admin->is_super;
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
        return $superAdmin->is_super && !$admin->is_super;
    }


    public function activateDoctor(Admin $admin)
    {
        return $admin->is_super || $admin->hasPermissionTo('control doctors');
    }
}
