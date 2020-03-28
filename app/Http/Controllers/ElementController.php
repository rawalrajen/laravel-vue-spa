<?php

namespace App\Http\Controllers;

use App\Element;
use App\Http\Resources\ElementResource;
use App\Repositories\ElementRepository;
use Illuminate\Database\DatabaseManager;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

/**
 * Class ElementController
 * @package App\Http\Controllers
 */
class ElementController extends Controller
{

    /**
     * @var DatabaseManager
     */
    protected $database;
    /**
     * @var ElementRepository
     */
    protected $element;

    /**
     * ElementController constructor.
     * @param DatabaseManager $database
     * @param ElementRepository $element
     */
    public function __construct(DatabaseManager $database, ElementRepository $element)
    {
        $this->database = $database;
        $this->element  = $element;
    }

    /**
     * Display a listing of the user resource.
     *
     * @param Request $request
     * @return AnonymousResourceCollection
     */
    public function index(Request $request)
    {
        return ElementResource::collection($this->element->all());
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param Request $request
     * @return ElementResource
     */
    public function store(Request $request)
    {
        $params  = $request->all();
        $element = $this->element->store([
            'name'     => $params['name'],
            'position' => $this->element->count()
        ]);
        return ElementResource::make($element);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param Request $request
     * @param Element $element
     * @return ElementResource
     */
    public function update(Request $request, Element $element)
    {
        $request->validate([
            'old_index' => 'required|numeric',
            'new_index' => 'required|numeric',
        ]);
        $oldPosition = $request->get('old_index');
        $newPosition = $request->get('new_index');

        $this->updateElementPosition($element, $oldPosition, $newPosition);

        return ElementResource::make($element);
    }

    /**
     * @param $element
     * @param $oldPosition
     * @param $newPosition
     */
    protected function updateElementPosition($element, $oldPosition, $newPosition)
    {
        //cyclic rotation upto new position
        if ($oldPosition < $newPosition) {
            $range = range($oldPosition + 1, $newPosition);

            foreach ($range as $position) {
                $this->element
                    ->findBy('position', $position)
                    ->update(['position' => $position - 1]);
            }
            $this->element
                ->findBy('position', $oldPosition)
                ->update(['position' => $newPosition]);
        }

        if ($oldPosition > $newPosition) {
            $range = range($newPosition, $oldPosition - 1);

            foreach ($range as $position) {
                $this->element
                    ->findBy('position', $position)
                    ->update(['position' => $position + 1]);
            }
            $this->element
                ->findBy('position', $oldPosition)
                ->update(['position' => $newPosition]);
        }
    }

}
