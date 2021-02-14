<?php

namespace App\Http\Controllers;

use App\Http\Requests\ChangePasswordRequest;
use App\Http\Requests\DoctorLoginRequest;
use App\Services\DoctorService;
use App\Traits\LoginTrait;
use App\Http\Requests\DoctorRegistrationRequest;
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
        // $token = "dYrgLmSME70mk04J7xVRK8:APA91bGiWwS05eMKVtWMz2M2grQqw6bsoxEyRSgUpgqk_2aWI3YnxJqz3tVbS5R9Bfrn-fUFZGZGcKszC9s_hmymc1mpIxPRHlwNrR3ZIMoGSt1yxF17Bm9YFv2_Lm3yjzUVa360Rez9";  
        // $from = "AAAA_YGBlGw:APA91bEdGi8Ond2Apc6neOfzklzaaBf7UPr3UUxySCslcjjkBRklDT7_MEWKazyDcdqAM0FhCeXu3L3A4PlhHPbO3EKucKb1oKgjsID9f0qgylcq2L_jjBujvr66-ZBYzWINiBdthW5q";
        // $msg = array
        //       (
        //         'body'  => "Testing Testing",
        //         'title' => "Hi, From Raj",
        //         'receiver' => 'erw',
        //         'icon'  => "https://image.flaticon.com/icons/png/512/270/270014.png",/*Default Icon*/
        //         'sound' => 'mySound'/*Default sound*/
        //       );

        // $fields = array
        //         (
        //             'to'        => $token,
        //             'notification'  => $msg
        //         );

        // $headers = array
        //         (
        //             'Authorization: key=' . $from,
        //             'Content-Type: application/json'
        //         );
        // //#Send Reponse To FireBase Server 
        // $ch = curl_init();
        // curl_setopt( $ch,CURLOPT_URL, 'https://fcm.googleapis.com/fcm/send' );
        // curl_setopt( $ch,CURLOPT_POST, true );
        // curl_setopt( $ch,CURLOPT_HTTPHEADER, $headers );
        // curl_setopt( $ch,CURLOPT_RETURNTRANSFER, true );
        // curl_setopt( $ch,CURLOPT_SSL_VERIFYPEER, false );
        // curl_setopt( $ch,CURLOPT_POSTFIELDS, json_encode( $fields ) );
        // $result = curl_exec($ch );
        // dd($result);
        // curl_close( $ch );
        $result = app(FirebaseService::class)->pushNotification();
        return response()->json($result);
    }
}
