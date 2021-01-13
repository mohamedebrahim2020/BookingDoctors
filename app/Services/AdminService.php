<?php

namespace App\Services;

use App\Mail\AdminPasswordMail;
use App\Repositories\AdminRepository;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Spatie\Permission\Models\Permission;

class AdminService extends BaseService
{
    public const is_super = 0;

    public function __construct(AdminRepository $repository)
    {
        $this->repository = $repository;
    }

    public function handleData($data)
    {
        $password = Str::random(10);
        $data['is_super'] = self::is_super;
        $data['password'] = Hash::make($password);
        $admin = $this->repository->store($data->all());
        $this->assignPermissions($data->permissions, $admin);
        // $this->sendEmail($admin, $password);
    }

    public function sendEmail($admin, $password)
    {
        Mail::to($admin->email)->send(new AdminPasswordMail($admin, $password));

    }

    public function assignPermissions(array $permissionIDs, $admin)
    {
        $permissions = [];
        foreach ($permissionIDs as $permissionID) {
            $permission = Permission::findById($permissionID);
            array_push($permissions, $permission); 
        }
        $admin->givePermissionTo($permissions);
        
    }

    // public function delete($id)
    // {
    //     return $this->repository->delete($id);
    // }

    
}