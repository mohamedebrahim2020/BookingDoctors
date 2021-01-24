<?php

namespace App\Repositories;

use App\Filters\AdminFilters;
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

   public function findAdminByEmail()
   {
    $admin = Admin::filter(app(AdminFilters::class))->firstorfail();
    return $admin;
   }
}