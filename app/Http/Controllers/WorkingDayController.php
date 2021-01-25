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
        $workingDay = $this->doctorService->addWorkingDay($request->except('is_all_day'));
        return response()->json(new CreatedResource($workingDay), Response::HTTP_CREATED);
    }
}
