<?php

namespace App\Http\Controllers;

use App\Enums\AppointmentStatus;
use App\Http\Requests\ApproveAppointmentRequest;
use App\Services\AppointmentService;
use App\Transformers\IndexAppointmentResource;
use Carbon\Carbon;
use Illuminate\Http\Request;
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
}
