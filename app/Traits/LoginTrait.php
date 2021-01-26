<?php

namespace App\Traits;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
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
        if ($tokens == null) {
            abort(Response::HTTP_BAD_REQUEST);
        } else {
            return $tokens;
        }
    }
}