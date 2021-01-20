<?php

namespace App\Http\Controllers;

use App\Filters\DoctorFilters;
use App\Http\Requests\DoctorLoginRequest;
use App\Http\Resources\TokenResource;
use App\Services\DoctorService;
use App\Traits\LoginTrait;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class DoctorController extends Controller
{
    use LoginTrait;

    protected $doctorService;

    public function __construct(DoctorService $doctorService)
    {
        $this->doctorService = $doctorService;
    }

    public function doctorLogin(DoctorLoginRequest $request , DoctorFilters $filters)
    {
        $this->doctorService->checkAuth($request->password, $filters);
        return response()->json(new TokenResource($this->login($request)), Response::HTTP_OK);
        
    }
}
