<?php

namespace App\Http\Controllers;

use App\Services\ReviewService;
use App\Transformers\IndexReviewResource;
use Illuminate\Http\Request;
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
        $reviews = $this->service->index();
        return response()->json(IndexReviewResource::collection($reviews), Response::HTTP_OK);       
    }
}
