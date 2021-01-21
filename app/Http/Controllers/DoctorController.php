<?php

namespace App\Http\Controllers;

use App\Filters\DoctorFilters;
use App\Http\Resources\IndexDoctorResource;
use App\Http\Resources\ShowDoctorResource;
use App\Services\DoctorService;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class DoctorController extends Controller
{
    protected $doctorService;

    public function __construct(DoctorService $doctorService)
    {
        $this->doctorService = $doctorService;
    }

    public function index(Request $request)
    {
        dd($request->all());
        $doctors = $this->doctorService->query($request);
        return response()->json(IndexDoctorResource::collection($doctors), Response::HTTP_OK);
    }

    public function show($doctor)
    {
        $doctor = $this->doctorService->show($doctor);
        return response()->json(new ShowDoctorResource($doctor), Response::HTTP_OK);
    }
}
