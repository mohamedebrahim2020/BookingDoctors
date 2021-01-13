<?php

namespace App\Repositories;

use App\Models\Admin;

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

   public function assignPermissions(array $permissions, $admin)
   {
        $admin->givePermissionTo($permissions);
        
   }

   public function updatePermissions(array $permissions, $adminId)
   {
       $this->find($adminId)->syncPermissions($permissions);
   }
}