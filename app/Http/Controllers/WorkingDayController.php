<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoringDoctorWorkingDayRequest;
use App\Services\DoctorService;
use Illuminate\Http\Request;

class WorkingDayController extends Controller
{
    protected $doctorService;

    public function __construct(DoctorService $doctorService)
    {
        $this->doctorService = $doctorService;
    }
    public function store(StoringDoctorWorkingDayRequest $request)
    {
        $this->doctorService->addWorkingDay($request->all());
    }
}
