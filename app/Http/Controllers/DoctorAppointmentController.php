<?php

namespace App\Http\Controllers;

use App\Http\Requests\PatientReserveAppointmentRequest;
use App\Services\DoctorService;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class DoctorAppointmentController extends Controller
{
    protected $doctorService;

    public function __construct(DoctorService $doctorService)
    {
        $this->doctorService = $doctorService;
        $this->middleware(function ($request, $next) {
            $doctor = $this->doctorService->show($request->doctor);
            if ($doctor->activated_at) {
                return $next($request);
            } else {
                abort(Response::HTTP_FORBIDDEN, 'doctor is not activated yet');
            }
        })->only('store');
    }

    public function store(PatientReserveAppointmentRequest $request)
    {
        $this->doctorService->storeAppointment($request->except('status','cancel_reason'), $request->doctor);
    }
}
