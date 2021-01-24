<?php

namespace App\Services;

use App\Mail\AdminPasswordMail;
use App\Models\Admin;
use App\Notifications\AdminRegistrationMail;
use App\Repositories\AdminRepository;
use App\Repositories\PermissionRepository;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class AdminService extends BaseService
{    
    public $permissionRepository;

    public function __construct(AdminRepository $repository, PermissionRepository $permissionRepository)
    {
        $this->repository = $repository;
        $this->permissionRepository = $permissionRepository;
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
        return $this->permissionRepository->all();
    }

    public function updateAdmin($request, $id)
    {
        $this->update($request->except('permissions'), $id);
        $this->repository->assignOrUpdatePermissions($request->permissions, $id);
    }

    public function checkAuth($data)
    {
        $admin = $this->repository->findAdminByEmail();
        (!Hash::check($data['password'], $admin->password)) ? abort(Response::HTTP_UNAUTHORIZED, 'unauthenticated') : "" ;      
    }
} 
