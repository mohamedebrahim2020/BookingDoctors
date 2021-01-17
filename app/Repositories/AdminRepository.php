<?php

namespace App\Repositories;

use App\Models\Admin;
use Spatie\Permission\Models\Permission;

class AdminRepository extends BaseRepository 
{
    /**
    * UserRepository constructor.
    *
    * @param User $model
    */
   public function __construct(Admin $model)
   {
       parent::__construct($model);
   }

   public function assignOrUpdatePermissions(array $permissions, $adminId)
   {
       $this->find($adminId)->syncPermissions($permissions);
   }

   public function allPermissions()
   {
       $permissions = Permission::all();
       return $permissions;
   }

   public function findAdminByEmail($email)
   {
       $admin = Admin::where('email', $email)->firstorfail();
       return $admin;
   }
}