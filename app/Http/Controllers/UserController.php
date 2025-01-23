<?php

namespace App\Http\Controllers;

use App\DTO\UserForm;
use App\Http\Requests\UserRequest;
use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Http\Request;

class UserController extends Controller
{
    /**
     * Display the specified resource.
     */
    public function show(): UserResource
    {
        return UserResource::make(auth()->user());
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UserRequest $request, User $user): UserResource
    {
        $userForm = UserForm::from($request->validated());

        $user->update($userForm->all());

        return UserResource::make($user);
    }
}
