<?php

namespace App\Http\Controllers;

use App\DTO\TagForm;
use App\Http\Requests\StoreTagRequest;
use App\Http\Requests\UpdateTagRequest;
use App\Http\Resources\TagResource;
use App\Models\Tag;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

/**
 * @group Теги
 */
class TagController extends Controller
{
    /**
     * Получить список тегов
     *
     * Возвращает пагинированный список всех тегов.
     *
     * @authenticated
     */
    public function index(): AnonymousResourceCollection
    {
        return TagResource::collection(Tag::query()->paginate());
    }

    /**
     * Создать новый тег
     *
     * Создает новый тег с заданным названием.
     *
     * @authenticated
     */
    public function store(StoreTagRequest $request): TagResource
    {
        $TagForm = TagForm::from($request->validated());

        $Tag = Tag::query()->create($TagForm->toArray());

        return TagResource::make(Tag::query()->find($Tag->uuid));
    }

    /**
     * Получить тег
     *
     * Возвращает информацию о конкретном теге по его идентификатору.
     *
     * @authenticated
     */
    public function show(Tag $Tag): TagResource
    {
        return TagResource::make($Tag);
    }

    /**
     * Обновить тег
     *
     * Обновляет информацию о существующем теге.
     *
     * @authenticated
     */
    public function update(UpdateTagRequest $request, Tag $Tag): TagResource
    {
        $TagForm = TagForm::from($request->validated());

        $Tag->update($TagForm->toArray());

        return TagResource::make(Tag::query()->find($Tag->uuid));
    }

    /**
     * Удалить тег
     *
     * Удаляет тег из системы.
     *
     * @authenticated
     * @response 204
     */
    public function destroy(Tag $Tag): \Illuminate\Http\Response
    {
        $Tag->delete();
        return response()->noContent();
    }
}
