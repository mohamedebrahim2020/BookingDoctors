<?php

namespace App\Http\Controllers;

use App\Http\Requests\AdminLoginRequest;
use App\Http\Resources\TokenResource;
use App\Models\Admin;
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

    public function adminLogin(AdminLoginRequest $request)
    {
        $admin = Admin::where('email', $request->username)->first();
        if (!$admin || !Hash::check($request->password, $admin->password, []))
        {
            abort(401, 'check your email or password');
        } else {
        return response()->json(new TokenResource($this->login($request)), Response::HTTP_OK);
        }
    }
}
