<?php

namespace App\Http\Controllers;

use App\Http\Requests\ChangePasswordRequest;
use App\Http\Requests\PatientLoginRequest;
use App\Http\Requests\PatientRegisterationRequest;
use App\Http\Requests\ResendVerificationCodeRequest;
use App\Http\Requests\StoreDeviceTokenRequest;
use App\Http\Requests\VerifyPatientEmailRequest;
use App\Services\FirebaseService;
use App\Services\PatientService;
use App\Traits\LoginTrait;
use App\Transformers\CreatedResource;
use App\Transformers\PatientProfileResource;
use App\Transformers\TokenResource;
use App\Transformers\UpdatedResource;
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

    public function codeResend(ResendVerificationCodeRequest $request)
    {
        $patient = $this->patientService->codeResend($request->all());
        return response(new UpdatedResource($patient), Response::HTTP_OK);
    }

    public function changePassword(ChangePasswordRequest $request)
    {
        $patient = $this->patientService->changePassword($request->all());
        return response()->json(new UpdatedResource($patient), Response::HTTP_OK);
    }

    public function profile()
    {
        $patient = $this->patientService->show(auth()->user()->id);
        return response()->json(new PatientProfileResource($patient), Response::HTTP_OK);
    }

    public function storeDeviceToken(StoreDeviceTokenRequest $request)
    {
        $token = app(FirebaseService::class)->storeDeviceToken($request->all());
        return response()->json(new CreatedResource($token), Response::HTTP_CREATED);        
    }
}
