<?php

namespace App\Http\Controllers;

use App\Transformers\IndexReviewResource;
use Illuminate\Http\Request;
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

    public function index()
    {
        $this->authorize('activateDoctor', Admin::class);
        $reviews = $this->service->index();
        return response()->json(IndexReviewResource::collection($reviews), Response::HTTP_OK); 
    }

    public function store(PatientStoreReviewRequest $request)
    {
        $review = $this->service->store($request->all('rank','comment'));
        return response()->json(new CreatedResource($review), Response::HTTP_CREATED);
    }
}
