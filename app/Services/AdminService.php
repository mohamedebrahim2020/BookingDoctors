<?php

namespace App\Services;

use App\Mail\AdminPasswordMail;
use App\Models\Admin;
use App\Notifications\AdminRegistrationMail;
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

    public function store($data)
    {
        $data['password'] = Str::random(10);
        $admin = $this->repository->store($data);
        $this->repository->assignOrUpdatePermissions($data['permissions'], $admin->id);
        $admin->notify(new AdminRegistrationMail($admin, $data['password']));
        return $admin;
    }

    public function permissions()
    {
        return $this->repository->allPermissions();
    }

    public function updateAdmin($request, $id)
    {
        $this->update($request->except('permissions'), $id);
        $this->repository->assignOrUpdatePermissions($request->permissions, $id);
    }

    public function checkAuth($data)
    {
        $admin = $this->repository->findAdminByEmail($data['username']);
        (!Hash::check($data['password'], $admin->password)) ? abort(401, 'unauthenticated') : "" ;      
    }
} 
