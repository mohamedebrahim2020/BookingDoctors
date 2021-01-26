<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoringDoctorWorkingDayRequest;
use App\Services\DoctorService;
use App\Transformers\CreatedResource;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class WorkingDayController extends Controller
{
    protected $doctorService;

    public function __construct(DoctorService $doctorService)
    {
        $this->doctorService = $doctorService;
    }
    public function store(StoringDoctorWorkingDayRequest $request)
    {        
        $workingDays = $this->doctorService->addWorkingDay($request->all());
        return response()->json(CreatedResource::collection($workingDays), Response::HTTP_CREATED);
    }
}
