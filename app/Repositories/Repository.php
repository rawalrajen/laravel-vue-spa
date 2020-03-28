<?php

namespace App\Repositories;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Application;


/**
 * Class Repository
 * @package App\Grievance\Repositories
 */
abstract class Repository implements RepositoryInterface
{
    /**
     * @var Builder
     */
    protected $model;

    /**
     * Repository constructor.
     *
     * @param Application $app
     */
    public function __construct(Application $app)
    {
        $this->model = $this->makeModel($app);
    }

    /**
     * Get model name with namespace
     *
     * @return String
     */
    abstract function getModel();

    /**
     * Get model
     *
     * @param Application $app
     *
     * @return Model
     */
    protected function makeModel($app)
    {
        return $app->make($this->getModel());
    }

    /**
     * Get all resources
     *
     * @param array $columns
     *
     * @return Collection
     */
    public function all($columns = array('*'))
    {
        return $this->model->all();
    }

    /**
     * Store newly created resource
     *
     * @param array $data
     *
     * @return Model
     */
    public function store(array $data)
    {
        return $this->model->create($data);
    }

    /**
     * Update specific resource.
     *
     * @param array $data
     * @param       $id
     *
     * @return bool
     */
    public function update($id, array $data)
    {
        return $this->model->find($id)->update($data);
    }

    /**
     * Update or create specific resource.
     *
     * @param array $data
     * @param       $id
     *
     * @return mixed
     */
    public function updateOrCreate($id, array $data)
    {
        return $this->model->updateOrCreate(['id' => $id], $data);
    }

    /**
     * Delete specific resource
     *
     * @param $id
     *
     * @return bool
     */
    public function delete($id)
    {
        return $this->model->destroy($id);
    }

    /**
     * Find specific resource
     *
     * @param       $id
     * @param array $columns
     *
     * @return Object
     */
    public function find($id, $columns = array('*'))
    {
        return $this->model->find($id, $columns);
    }

    /**
     * Find specific resource by given attribute
     *
     * @param       $attribute
     * @param       $value
     * @param array $columns
     *
     * @return Object
     */
    public function findBy($attribute, $value, $columns = array('*'))
    {
        return $this->model
            ->where($attribute, '=', $value)
            ->orderBy('updated_at')
            ->first($columns);
    }

    /**
     * Find specific model by given attributes or new model
     * @param array $attributes
     * @param $values
     * @return Builder|Model
     */
    public function firstOrNew($attributes = [], $values = [])
    {
        return $this->model->firstOrNew($attributes);
    }

    public function count()
    {
        return $this->model->count();
    }
}
