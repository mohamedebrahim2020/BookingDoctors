<?php

namespace App\Services;

use App\Repositories\ReviewRepository;

class ReviewService extends BaseService
{
    public function __construct(ReviewRepository $repository)
    {
        $this->repository = $repository;
    }

    public function index()
    {
        $reviews = $this->repository->index();
    }

}    