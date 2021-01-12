<?php

namespace App\Http\Controllers;

use App\Http\Requests\AdminLoginRequest;
use App\Models\Admin;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Route;
use Laravel\Passport\Client;

class LoginController extends Controller
{
    function adminRegister(Request $request)
    {   
    /**
     * Get a validator for an incoming registration request.
     *
     * @param  array  $request
     * @return \Illuminate\Contracts\Validation\Validator
     */
    $valid = validator($request->only('email', 'name', 'password','phone'), [
        'name' => 'required|string|max:100',
        'email' => 'required|string|email|max:255|unique:admins,email',
        'password' => 'required|min:6',
        'phone' => 'required|unique:admins,phone',
    ]);
//is_super
    if ($valid->fails()) {
        // $jsonError=response()->json($valid->errors()->all(), 400);
        // return Response::json($jsonError);
        return response()->json($valid->errors()->all(), 422);
    }

    $data = request()->only('email','name','password','phone');

    $user = Admin::create([
        'name' => $data['name'],
        'email' => $data['email'],
        'password' => bcrypt($data['password']),
        'phone' => $data['phone'],
        'is_super' => 0,
    ]);

    // And created user until here.

    $client = Client::where('id', 1)->first();

    // Is this $request the same request? I mean Request $request? Then wouldn't it mess the other $request stuff? Also how did you pass it on the $request in $proxy? Wouldn't Request::create() just create a new thing?

    $request->request->add([
        'grant_type'    => 'password',
        'client_id'     => $client->id,
        'client_secret' => $client->secret,
        'username'      => $data['email'],
        'password'      => $data['password'],
        'scope'         => ['*'],
    ]);

    // Fire off the internal request. 
    $token = Request::create(
        'oauth/token',
        'POST'
    );
    
    return Route::dispatch($token);
    }

    public function adminLogin(AdminLoginRequest $request)
    {
        
        $request->request->add([
            'grant_type'    => $request->grant_type,
            'client_id'     => $request->client_id,
            'client_secret' => $request->client_secret,
            'username'      => $request->username,
            'password'      => $request->password,
            'scope'         => ['*'],
        ]);
 
        
        $token = Request::create(
            'oauth/token',
            'POST'
        );
        
        return Route::dispatch($token);
    }
}
