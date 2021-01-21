<?php

namespace App\Http\Controllers;

use App\Http\Requests\DoctorRegistrationRequest;
use App\Http\Requests\StoreDoctorRequest;
use App\Http\Resources\CreatedDoctorResource;
use App\Services\DoctorService;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class DoctorController extends Controller
{
    protected $doctorService;

    public function __construct(DoctorService $doctorService)
    {
        $this->doctorService = $doctorService;
    }
    public function register(DoctorRegistrationRequest $request)
    {
        $doctor = $this->doctorService->store($request->except('activated_at'));
        return response()->json(new CreatedDoctorResource($doctor), Response::HTTP_CREATED);
    }
}
