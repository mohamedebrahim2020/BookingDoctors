<?php

namespace App\Http\Controllers;

use App\Http\Requests\PatientLoginRequest;
use App\Http\Requests\PatientRegisterationRequest;
use App\Http\Requests\VerifyPatientEmailRequest;
use App\Services\PatientService;
use App\Traits\LoginTrait;
use App\Transformers\CreatedResource;
use App\Transformers\TokenResource;
use Illuminate\Http\Response;

class PatientController extends Controller
{
    use LoginTrait;

    public $patientService;

    public function __construct(PatientService $patientService)
    {
        $this->patientService = $patientService;
    }

    public function register(PatientRegisterationRequest $request)
    {
        $patient = $this->patientService->store($request->except('verified_at'));
        return response()->json(new CreatedResource($patient), Response::HTTP_CREATED);
    }

    public function verify (VerifyPatientEmailRequest $request)
    {
        $this->patientService->checkCode($request->all());
        return response()->json([], Response::HTTP_OK);
    }

    public function login(PatientLoginRequest $request)
    {
        $this->patientService->checkAuth($request->all());
        return response()->json(new TokenResource($this->requestTokensFromPassport($request)), Response::HTTP_OK);
    }
}
