<?php

namespace App\Http\Controllers;

use App\Services\ReviewService;
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
        $this->service->delete($this->service->show($id));
        return response()->json([], Response::HTTP_OK);       
    }
}
