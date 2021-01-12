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
}