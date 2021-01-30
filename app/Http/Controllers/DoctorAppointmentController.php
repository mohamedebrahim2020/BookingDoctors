<?php

namespace App\Http\Controllers;

use App\Http\Requests\PatientReserveAppointmentRequest;
use App\Services\DoctorService;
use App\Services\PatientService;
use App\Transformers\CreatedResource;
use Illuminate\Http\Response;

class DoctorAppointmentController extends Controller
{
    protected $doctorService;
    protected $patientService;

    public function __construct(DoctorService $doctorService, PatientService $patientService)
    {
        $this->doctorService = $doctorService;
        $this->patientService = $patientService;
    }

    public function store(PatientReserveAppointmentRequest $request)
    {
        $this->doctorService->checkDoctorIsActivated($request->doctor);
        $appointment = $this->patientService->storeAppointment($request->except('status','cancel_reason'), $request->doctor);
        return response()->json(new CreatedResource($appointment), Response::HTTP_CREATED);
    }
}
