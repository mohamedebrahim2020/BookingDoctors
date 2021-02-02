<?php

namespace App\Http\Controllers;

use App\Services\AppointmentService;
use App\Transformers\IndexAppointmentResource;
use App\Http\Requests\PatientReserveAppointmentRequest;
use App\Transformers\CreatedResource;
use Illuminate\Http\Response;

class AppointmentController extends Controller
{
    protected $service;

    public function __construct(AppointmentService $service)
    {
        $this->service = $service;
    }

    public function index()
    {
        $appointments = $this->service->index();
        return response()->json(IndexAppointmentResource::collection($appointments), Response::HTTP_OK);
    }

    public function approve()
    {
        $this->service->approve();
        return response()->json([], Response::HTTP_OK);
    }    
    public function store(PatientReserveAppointmentRequest $request)
    {
        $appointment = $this->service->store($request->except('status','cancel_reason'), $request->doctor);
        return response()->json(new CreatedResource($appointment), Response::HTTP_CREATED);
    }
}
