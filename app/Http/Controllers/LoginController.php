<?php

namespace App\Http\Controllers;

use App\Http\Requests\AdminLoginRequest;
use App\Http\Resources\TokenResource;
use App\Models\Admin;
use App\Services\AdminService;
use App\Traits\LoginTrait;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Route;
use Laravel\Passport\Client;

class LoginController extends Controller
{
    use LoginTrait;

    protected $adminService;

    public function __construct(AdminService $adminService)
    {
        $this->adminService = $adminService;
    }

    public function adminLogin(AdminLoginRequest $request)
    {
        $this->adminService->checkAuth($request->all());
        return response()->json(new TokenResource($this->login($request)), Response::HTTP_OK);
        
    }
}
