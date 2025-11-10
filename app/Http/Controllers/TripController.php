<?php

namespace App\Http\Controllers;

use App\DTO\TripForm;
use App\Http\Requests\StoreTripRequest;
use App\Http\Requests\UpdateTripRequest;
use App\Http\Resources\TripResource;
use App\Models\Trip;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

/**
 * @group Путешествия
 */
class TripController extends Controller
{
    /**
     * Получить список путешествий
     *
     * Возвращает пагинированный список всех путешествий пользователя.
     *
     * @authenticated
     */
    public function index(): AnonymousResourceCollection
    {
        return TripResource::collection(Trip::query()->paginate());
    }

    /**
     * Создать новое путешествие
     *
     * Создает новое путешествие с заданными параметрами.
     *
     * @authenticated
     */
    public function store(StoreTripRequest $request): TripResource
    {
        $TripForm = TripForm::from($request->validated());

        $Trip = Trip::query()->create($TripForm->toArray());

        return TripResource::make(Trip::query()->find($Trip->uuid));
    }

    /**
     * Получить путешествие
     *
     * Возвращает информацию о конкретном путешествии по его идентификатору.
     *
     * @authenticated
     */
    public function show(Trip $Trip): TripResource
    {
        return TripResource::make($Trip);
    }

    /**
     * Обновить путешествие
     *
     * Обновляет информацию о существующем путешествии.
     *
     * @authenticated
     */
    public function update(UpdateTripRequest $request, Trip $Trip): TripResource
    {
        $TripForm = TripForm::from($request->validated());

        $Trip->update($TripForm->toArray());

        return TripResource::make(Trip::query()->find($Trip->uuid));
    }

    /**
     * Удалить путешествие
     *
     * Удаляет путешествие из системы.
     *
     * @authenticated
     * @response 204
     */
    public function destroy(Trip $Trip): \Illuminate\Http\Response
    {
        $Trip->delete();
        return response()->noContent();
    }
}
