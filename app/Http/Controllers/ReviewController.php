<?php

namespace App\Http\Controllers;

use App\Http\Requests\DoctorRespondReviewRequest;
use App\Transformers\UpdatedResource;
use App\Http\Requests\PatientStoreReviewRequest;
use App\Services\ReviewService;
use App\Transformers\CreatedResource;
use Illuminate\Http\Response;

class ReviewController extends Controller
{
    public $service;

    public function __construct(ReviewService $service)
    {
        $this->service = $service;
    }

    public function respond(DoctorRespondReviewRequest $request, $id)
    {
        $this->service->respond($request->all('respond'), $id);
        return response()->json([], Response::HTTP_OK);
    }

    public function store(PatientStoreReviewRequest $request)
    {
        $review = $this->service->store($request->all('rank','comment'));
        return response()->json(new CreatedResource($review), Response::HTTP_CREATED);
    }
}
