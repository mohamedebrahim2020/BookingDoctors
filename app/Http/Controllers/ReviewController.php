<?php

namespace App\Http\Controllers;

use App\Services\ReviewService;
use App\Transformers\UpdatedResource;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class ReviewController extends Controller
{
    public $service;

    public function __construct(ReviewService $service)
    {
        $this->service = $service;
    }

    public function delete($id)
    {
        $review = $this->service->show($id);
        $this->service->delete($review);
        return response()->json(new UpdatedResource($review), Response::HTTP_OK);       
    }
}
