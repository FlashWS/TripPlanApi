<?php

namespace App\Http\Controllers;

use App\DTO\PointForm;
use App\Http\Requests\StorePointRequest;
use App\Http\Requests\UpdatePointRequest;
use App\Http\Resources\PointResource;
use App\Models\Point;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

/**
 * @group Точки
 */
class PointController extends Controller
{
    /**
     * Получить список точек
     *
     * Возвращает пагинированный список всех точек пользователя.
     *
     * @authenticated
     */
    public function index(): AnonymousResourceCollection
    {
        return PointResource::collection(Point::query()->with('tags')->paginate());
    }

    /**
     * Создать новую точку
     *
     * Создает новую точку с заданными координатами и описанием.
     *
     * @authenticated
     */
    public function store(StorePointRequest $request): PointResource
    {
        $pointForm = PointForm::from($request->validated());

        $point = Point::query()->create($pointForm->except('tags')->toArray());

        if ($pointForm->tags) {
            $point->tags()->sync($pointForm->tags);
        }

        return PointResource::make(Point::query()->with('tags')->find($point->uuid));
    }

    /**
     * Получить точку
     *
     * Возвращает информацию о конкретной точке по её идентификатору.
     *
     * @authenticated
     */
    public function show(Point $point): PointResource
    {
        return PointResource::make($point->load('tags'));
    }

    /**
     * Обновить точку
     *
     * Обновляет информацию о существующей точке.
     *
     * @authenticated
     */
    public function update(UpdatePointRequest $request, Point $point): PointResource
    {
        $pointForm = PointForm::from($request->validated());

        $point->update($pointForm->except('tags')->toArray());

        if ($pointForm->tags !== null) {
            $point->tags()->sync($pointForm->tags);
        }

        return PointResource::make(Point::query()->with('tags')->find($point->uuid));
    }

    /**
     * Удалить точку
     *
     * Удаляет точку из системы.
     *
     * @authenticated
     * @response 204
     */
    public function destroy(Point $point): \Illuminate\Http\Response
    {
        $point->delete();
        return response()->noContent();
    }
}
