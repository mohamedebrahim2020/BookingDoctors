<?php

namespace App\Http\Controllers;

use App\Http\Requests\DoctorRespondReviewRequest;
use App\Services\ReviewService;
use App\Transformers\UpdatedResource;
use Illuminate\Http\Response;

class ReviewController extends Controller
{
    public $service;

    public function __construct(ReviewService $service)
    {
        $this->service = $service;
    }

    public function update(DoctorRespondReviewRequest $request, $id)
    {
        $review = $this->service->show($id);
        // $patient = $review->appointment->patient;
        // dd($patient->firebaseTokens);
        $this->service->update($request->all('respond'), $review->id);
        return response()->json(new UpdatedResource($review), Response::HTTP_OK);
    }
}
