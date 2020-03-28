<?php


namespace App\Repositories;


use App\Element;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

/**
 * Class ElementRepository
 * @package App\Repositories
 */
class ElementRepository extends Repository
{
    /**
     * @return string
     */
    public function getModel()
    {
        return Element::class;
    }

    /**
     * Get all the models
     *
     * @param array $columns
     *
     * @return Builder[]|Collection|Model[]
     */
    public function all($columns = array('*'))
    {
        return $this->model
            ->orderBy('position', 'asc')->get();
    }
}
