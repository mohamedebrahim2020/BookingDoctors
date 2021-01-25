<?php

namespace App\Http\Controllers;

use App\Services\SpecializationService;
use App\Transformers\SpecializationResource;
use Illuminate\Http\Response;

class SpecializationController extends Controller
{
    protected $specializationService;

    public function __construct(SpecializationService $specializationService)
    {
        $this->specializationService = $specializationService;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $specializations = $this->specializationService->index();
        return response()->json(SpecializationResource::collection($specializations), Response::HTTP_OK);
    }
}
