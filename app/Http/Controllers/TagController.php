<?php

namespace App\Http\Controllers;

use App\DTO\TagForm;
use App\Http\Requests\StoreTagRequest;
use App\Http\Requests\UpdateTagRequest;
use App\Http\Resources\TagResource;
use App\Models\Tag;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class TagController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): AnonymousResourceCollection
    {
        return TagResource::collection(Tag::query()->paginate());
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreTagRequest $request)
    {
        $TagForm = TagForm::from($request->validated());

        $Tag = Tag::query()->create($TagForm->toArray());

        return TagResource::make(Tag::query()->find($Tag->uuid));
    }

    /**
     * Display the specified resource.
     */
    public function show(Tag $Tag)
    {
        return TagResource::make($Tag);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateTagRequest $request, Tag $Tag): TagResource
    {
        $TagForm = TagForm::from($request->validated());

        $Tag->update($TagForm->toArray());

        return TagResource::make(Tag::query()->find($Tag->uuid));
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Tag $Tag)
    {
        $Tag->delete();
    }
}
