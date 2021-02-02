<?php

namespace App\Http\Controllers;

use App\Http\Requests\PatientReserveAppointmentRequest;
use App\Http\Requests\RejectAppointmentRequest;
use App\Services\AppointmentService;
use App\Services\DoctorService;
use App\Services\PatientService;
use App\Transformers\CreatedResource;
use App\Transformers\UpdatedResource;
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

    public function reject(RejectAppointmentRequest $request, $id)
    {
        $appointment = $this->service->reject($request->except('status'), $id);
        return response()->json(new UpdatedResource($appointment), Response::HTTP_OK);        
    }
}
