<?php

namespace App\Http\Controllers;

use App\Filters\DoctorFilters;
use App\Http\Resources\IndexDoctorResource;
use App\Http\Resources\ShowDoctorResource;
use App\Models\Doctor;
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
        // $dd = Doctor::filter($filters)->get();
        // dd($dd);
        $doctors = $this->doctorService->query($request);
        // $doctors = $this->doctorService->unactivatedDoctors();
        return response()->json(IndexDoctorResource::collection($doctors), Response::HTTP_OK);
    }

    public function show($doctor)
    {
        $doctor = $this->doctorService->unactivatedDoctor($doctor);
        return response()->json(new ShowDoctorResource($doctor), Response::HTTP_OK);
    }
}
