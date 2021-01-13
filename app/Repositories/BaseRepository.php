<?php

namespace App\Repositories;

use Illuminate\Container\Container as Application;
use Illuminate\Database\Eloquent\Model;

class BaseRepository
{
        /**      
     * @var Model      
     */     
    protected $model;       

    /**      
     * BaseRepository constructor.      
     *      
     * @param Model $model      
     */     
    public function __construct(Model $model)     
    {         
        $this->model = $model;
    }
 
    /**
    * @param array $attributes
    *
    * @return Model
    */
    public function store(array $attributes): Model
    {
        return $this->model->create($attributes);
    }

        /**
    * @param array $attributes
    *
    * @return Model
    */
    public function update(array $attributes, $id)
    {
        return $this->find($id)->update($attributes);
    }
 
    /**
    * @param $id
    * @return Model
    */
    public function find($id): ?Model
    {
        return $this->model->find($id);
    }

        /**
    * @param $id
    * @return Model
    */
    public function delete($model)
    {   
         $model->delete();
    }
}
