<?php

namespace App\Http\Controllers;

use App\Http\Requests\AdminLoginRequest;
use App\Http\Requests\DoctorLoginRequest;
use App\Http\Resources\TokenResource;
use App\Models\Admin;
use App\Models\Doctor;
use App\Services\AdminService;
use App\Services\DoctorService;
use App\Traits\LoginTrait;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Route;
use Laravel\Passport\Client;

class LoginController extends Controller
{
    

    protected $adminService;
    protected $doctorService;

    public function __construct(AdminService $adminService, DoctorService $doctorService)
    {
        $this->adminService = $adminService;
        $this->doctorService = $doctorService;
    }

 


}
