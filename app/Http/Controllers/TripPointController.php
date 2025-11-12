<?php

namespace App\Http\Controllers;

use App\DTO\TripPointForm;
use App\Http\Requests\StoreTripPointRequest;
use App\Http\Requests\UpdateTripPointRequest;
use App\Http\Resources\TripPointResource;
use App\Models\Trip;
use App\Models\TripPoint;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

/**
 * @group Точки путешествия
 */
class TripPointController extends Controller
{
    /**
     * Получить список точек путешествия
     *
     * Возвращает список всех точек в путешествии, отсортированных по дню и порядку.
     *
     * @authenticated
     */
    public function index(Trip $trip): AnonymousResourceCollection
    {
        $tripPoints = TripPoint::query()
            ->where('trip_uuid', $trip->uuid)
            ->with('point.tags')
            ->orderBy('day')
            ->orderBy('order')
            ->get();

        return TripPointResource::collection($tripPoints);
    }

    /**
     * Добавить точку в путешествие
     *
     * Добавляет существующую точку в путешествие с указанием дня, времени и порядка.
     *
     * @authenticated
     */
    public function store(StoreTripPointRequest $request, Trip $trip): TripPointResource
    {
        $tripPointForm = TripPointForm::from($request->validated());

        $tripPoint = new TripPoint($tripPointForm->toArray());
        $tripPoint->trip_uuid = $trip->uuid;
        $tripPoint->save();

        return TripPointResource::make($tripPoint->load('point.tags'));
    }

    /**
     * Получить точку путешествия
     *
     * Возвращает информацию о конкретной точке в путешествии.
     *
     * @authenticated
     */
    public function show(TripPoint $tripPoint): TripPointResource
    {
        return TripPointResource::make($tripPoint->load('point.tags'));
    }

    /**
     * Обновить точку путешествия
     *
     * Обновляет информацию о точке в путешествии (день, время, порядок, примечание).
     *
     * @authenticated
     */
    public function update(UpdateTripPointRequest $request, TripPoint $tripPoint): TripPointResource
    {
        $tripPointForm = TripPointForm::from($request->validated());

        $tripPoint->update($tripPointForm->toArray());

        return TripPointResource::make($tripPoint->load('point.tags'));
    }

    /**
     * Удалить точку из путешествия
     *
     * Удаляет точку из путешествия (не удаляет саму точку).
     *
     * @authenticated
     * @response 204
     */
    public function destroy(TripPoint $tripPoint): \Illuminate\Http\Response
    {
        $tripPoint->delete();
        return response()->noContent();
    }
}
