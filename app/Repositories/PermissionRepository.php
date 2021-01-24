<?php

namespace App\Repositories;

use Spatie\Permission\Models\Permission;

class PermissionRepository extends BaseRepository 
{
    /**
    * UserRepository constructor.
    *
    * @param Specialization $model
    */
   public function __construct(Permission $model)
   {
       parent::__construct($model);
   }
   
} 