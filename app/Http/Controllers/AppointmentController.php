<?php

namespace App\Http\Controllers;

use App\Http\Requests\PatientReserveAppointmentRequest;
use App\Services\AppointmentService;
use App\Services\DoctorService;
use App\Services\PatientService;
use App\Transformers\CreatedResource;
use Illuminate\Http\Response;

class AppointmentController extends Controller
{
    protected $appointmentService;

    public function __construct(AppointmentService $appointmentService)
    {
        $this->appointmentService = $appointmentService;
    }

    public function store(PatientReserveAppointmentRequest $request)
    {
        $appointment = $this->appointmentService->storeAppointment($request->except('status','cancel_reason'), $request->doctor);
        return response()->json(new CreatedResource($appointment), Response::HTTP_CREATED);
    }
}
