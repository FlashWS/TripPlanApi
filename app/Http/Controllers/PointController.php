<?php

namespace App\Http\Controllers;

use App\DTO\PointForm;
use App\Http\Requests\StorePointRequest;
use App\Http\Requests\UpdatePointRequest;
use App\Http\Resources\PointResource;
use App\Models\Point;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class PointController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): AnonymousResourceCollection
    {
        return PointResource::collection(Point::query()->paginate());
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StorePointRequest $request)
    {
        $pointForm = PointForm::from($request->validated());

        $point = Point::query()->create($pointForm->toArray());

        return PointResource::make(Point::query()->find($point->uuid));
    }

    /**
     * Display the specified resource.
     */
    public function show(Point $point)
    {
        return PointResource::make($point);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdatePointRequest $request, Point $point): PointResource
    {
        $pointForm = PointForm::from($request->validated());

        $point->update($pointForm->toArray());

        return PointResource::make(Point::query()->find($point->uuid));
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Point $point)
    {
        $point->delete();
    }
}
