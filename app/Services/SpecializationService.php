<?php

namespace App\Services;

use App\Repositories\SpecializationRepository;

class SpecializationService extends BaseService
{
    public function __construct(SpecializationRepository $repository)
    {
        $this->repository = $repository;
    }
}