<?php

namespace App\Traits;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

trait LoginTrait 
{
    public function requestTokensFromPassport($request)
    {
        request()->request->add([
            'grant_type'    => $request->grant_type,
            'client_id'     => $request->client_id,
            'client_secret' => $request->client_secret,
            'username'      => $request->email,
            'password'      => $request->password,
            'scope'         => ['*'],
        ]);
        $tokensApi = Request::create(
            'oauth/token',
            'POST'
        );
        $tokens = json_decode(Route::dispatch($tokensApi)->getContent());
        return $tokens;
    }
}