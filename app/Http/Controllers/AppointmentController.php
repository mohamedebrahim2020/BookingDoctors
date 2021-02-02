<?php

namespace App\Http\Controllers;

use App\Http\Requests\PatientReserveAppointmentRequest;
use App\Services\AppointmentService;
use App\Services\DoctorService;
use App\Services\PatientService;
use App\Transformers\CreatedResource;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class AppointmentController extends Controller
{
    protected $service;

    public function __construct(AppointmentService $service)
    {
        $this->service = $service;
    }

    public function store(PatientReserveAppointmentRequest $request)
    {
        $appointment = $this->service->store($request->except('status','cancel_reason'), $request->doctor);
        return response()->json(new CreatedResource($appointment), Response::HTTP_CREATED);
    }

    public function reject(Request $request)
    {
        
    }
}
