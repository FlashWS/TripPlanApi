<?php

namespace App\Http\Controllers;

use App\DTO\TripForm;
use App\Http\Requests\StoreTripRequest;
use App\Http\Requests\UpdateTripRequest;
use App\Http\Resources\TripResource;
use App\Models\Trip;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class TripController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): AnonymousResourceCollection
    {
        return TripResource::collection(Trip::query()->paginate());
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreTripRequest $request)
    {
        $TripForm = TripForm::from($request->validated());

        $Trip = Trip::query()->create($TripForm->toArray());

        return TripResource::make(Trip::query()->find($Trip->uuid));
    }

    /**
     * Display the specified resource.
     */
    public function show(Trip $Trip)
    {
        return TripResource::make($Trip);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateTripRequest $request, Trip $Trip): TripResource
    {
        $TripForm = TripForm::from($request->validated());

        $Trip->update($TripForm->toArray());

        return TripResource::make(Trip::query()->find($Trip->uuid));
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Trip $Trip)
    {
        $Trip->delete();
    }
}
