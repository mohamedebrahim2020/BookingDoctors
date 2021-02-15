<?php

namespace App\Http\Controllers;

use App\Http\Requests\ChangePasswordRequest;
use App\Http\Requests\DoctorLoginRequest;
use App\Services\DoctorService;
use App\Traits\LoginTrait;
use App\Http\Requests\DoctorRegistrationRequest;
use App\Http\Requests\StoreDeviceTokenRequest;
use App\Services\FirebaseService;
use App\Transformers\CreatedResource;
use App\Transformers\DoctorProfileResource;
use App\Transformers\IndexDoctorResource;
use App\Transformers\ShowDoctorResource;
use App\Transformers\TokenResource;
use App\Transformers\UpdatedResource;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class DoctorController extends Controller
{
    use LoginTrait;

    protected $doctorService;

    public function __construct(DoctorService $doctorService)
    {
        $this->doctorService = $doctorService;
    }

    public function login(DoctorLoginRequest $request)
    {
        $this->doctorService->checkAuth($request->all());
        return response()->json(new TokenResource($this->requestTokensFromPassport($request)), Response::HTTP_OK);
    }
  
    public function index(Request $request)
    {
        $doctors = $this->doctorService->query($request->all());
        return response()->json(IndexDoctorResource::collection($doctors), Response::HTTP_OK);
    }

    public function show($doctor)
    {
        $doctor = $this->doctorService->show($doctor);
        return response()->json(new ShowDoctorResource($doctor), Response::HTTP_OK);
    }
  
    public function register(DoctorRegistrationRequest $request)
    {
        $doctor = $this->doctorService->store($request->except('activated_at'));
        return response()->json(new CreatedResource($doctor), Response::HTTP_CREATED);
    }

    public function changePassword(ChangePasswordRequest $request)
    {
        $doctor = $this->doctorService->changePassword($request->all());
        return response()->json(new UpdatedResource($doctor), Response::HTTP_OK);
    }

    public function profile()
    {
        $doctor = $this->doctorService->show(auth()->user()->id);
        return response()->json(new ShowDoctorResource($doctor), Response::HTTP_OK);
    }

    public function push()
    {
        $result = app(FirebaseService::class)->pushNotification();
        return response()->json($result);
    }

    public function storeDeviceToken(StoreDeviceTokenRequest $request)
    {
        $token = $this->doctorService->storeDeviceToken($request->all());        
    }
}
