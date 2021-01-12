<?php

namespace App\Services;

use App\Repositories\BaseRepository;

class BaseService
{
    protected $repository;
    public $relations;
    public $pagination;
    public function __construct(BaseRepository $repository)
    {
        $this->repository = $repository;
    }

    public function index()
    {

    }
    public function show($id)
    {
        return $this->repository->find($id);
    }
    public function store($data)
    {
        return $this->repository->store($data, false);
    }
    // public function update(array $data, $id, $resource = true)
    // {
    //     return $this->repository->update($data, $id, false, $resource);
    // }
    // public function delete($id)
    // {
    //     $this->repository->delete($id);
    // }
}
