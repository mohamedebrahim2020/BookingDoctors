<?php

namespace App\Http\Controllers;

use App\Models\Doctor;
use App\Services\DoctorService;
use App\Transformers\IndexDoctorReviewsResource;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class DoctorReviewController extends Controller
{
    public $service;

    public function __construct(DoctorService $service)
    {
        $this->service = $service;
    }

    public function index()
    {
        $reviews = $this->service->getReviews();
        return response()->json(['reviews' => IndexDoctorReviewsResource::collection($reviews), 'averageRank'=> $reviews->avg('rank')], Response::HTTP_OK);
    }
}
