<?php


namespace App\Repositories;


use App\Element;

class ElementRepository extends Repository
{
    public function getModel()
    {
        return Element::class;
    }

    public function all($columns = array('*'))
    {
        return $this->model
            ->orderBy('position', 'asc')->get();
    }
}
