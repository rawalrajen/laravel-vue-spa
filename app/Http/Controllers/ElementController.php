<?php

namespace App\Http\Controllers;

use App\Element;
use App\Http\Resources\ElementResource;
use App\Repositories\ElementRepository;
use Exception;
use Illuminate\Database\DatabaseManager;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Response;
use Throwable;

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
    public function __construct(
        DatabaseManager $database,
        ElementRepository $element
    )
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
        $request->validate([
            'name' => 'required|string:max,255'
        ]);
        try {
            $params  = $request->all();
            $element = $this->element->store([
                'name'     => $params['name'],
                'position' => $this->element->count()
            ]);
            return ElementResource::make($element);
        } catch (Exception $e) {

            return response()->json($e->getMessage(), $e->getCode());
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param Request $request
     * @param Element $element
     * @return ElementResource
     * @throws Throwable
     */
    public function update(Request $request, Element $element)
    {
        $request->validate([
            'old_index' => 'required|numeric',
            'new_index' => 'required|numeric',
        ]);
        $this->database->beginTransaction();
        try {
            $oldPosition = $request->get('old_index');
            $newPosition = $request->get('new_index');
            $this->updateElementPositions($oldPosition, $newPosition);
            $this->database->commit();

            return ElementResource::make($element);
        } catch (Exception $e) {
            $this->database->rollBack();

            return response()->json($e->getMessage(), Response::HTTP_UNPROCESSABLE_ENTITY);
        }

    }

    /**
     * Update element positions
     *
     * @param $oldPosition
     * @param $newPosition
     */
    protected function updateElementPositions($oldPosition, $newPosition)
    {
        //handle downward drag and drop
        if ($oldPosition < $newPosition) {
            $displacement = range($oldPosition + 1, $newPosition);

            foreach ($displacement as $position) {
                $this->element
                    ->findBy('position', $position)
                    ->update(['position' => $position - 1]);
            }
            $this->element
                ->findBy('position', $oldPosition)
                ->update(['position' => $newPosition]);
        }

        //handle upward drag and drop
        if ($oldPosition > $newPosition) {
            $displacement = range($newPosition, $oldPosition - 1);

            foreach ($displacement as $position) {
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
