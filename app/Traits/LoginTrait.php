<?php

namespace App\Traits;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

trait LoginTrait 
{
    public function login($request)
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
        return($token);
    }
}