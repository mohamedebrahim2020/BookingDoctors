<?php

namespace App\Services;

use App\Repositories\SpecializationRepository;
use Illuminate\Support\Facades\Cache;

class SpecializationService extends BaseService
{
    public function __construct(SpecializationRepository $repository)
    {
        $this->repository = $repository;
    }

    public function index()
    {
        $specializations = Cache::remember('specializations', 33600 , function () {
            return $this->repository->all();
        });
        return $specializations;
    }
}
