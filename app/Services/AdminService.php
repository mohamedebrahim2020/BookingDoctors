<?php

namespace App\Services;

use App\Mail\AdminPasswordMail;
use App\Models\Admin;
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
        $this->repository->assignPermissions($data->permissions, $admin);
        // $this->sendEmail($admin, $password);
    }

    public function sendEmail($admin, $password)
    {
        Mail::to($admin->email)->send(new AdminPasswordMail($admin, $password));

    }



    public function updateAdmin($request, $id)
    {
        $this->update($request->except('permissions'), $id);
        $this->repository->updatePermissions($request->permissions, $id);
    }

    
}