<?php

namespace App\Http\Controllers;

use App\Http\Requests\DoctorRegistrationRequest;
use App\Http\Resources\CreatedDoctorResource;
use App\Services\DoctorService;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class RegisterController extends Controller
{
    protected $doctorService;

    public function __construct(DoctorService $doctorService)
    {
        $this->doctorService = $doctorService;
    }
    public function doctorRegister(DoctorRegistrationRequest $request)
    {
        $doctor = $this->doctorService->store($request->all());
        return response()->json(new CreatedDoctorResource($doctor), Response::HTTP_CREATED);
    }
}
