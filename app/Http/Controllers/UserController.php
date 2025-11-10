<?php

namespace App\Http\Controllers;

use App\DTO\UserForm;
use App\Http\Requests\UserRequest;
use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Http\Request;

/**
 * @group Пользователь
 */
class UserController extends Controller
{
    /**
     * Получить профиль текущего пользователя
     *
     * Возвращает информацию о профиле авторизованного пользователя.
     *
     * @authenticated
     */
    public function show(): UserResource
    {
        return UserResource::make(auth()->user());
    }

    /**
     * Обновить профиль пользователя
     *
     * Обновляет информацию профиля авторизованного пользователя.
     *
     * @authenticated
     */
    public function update(UserRequest $request): UserResource
    {
        $user = auth()->user();

        $userForm = UserForm::from($request->validated());

        $user->update($userForm->all());

        return UserResource::make($user);
    }
}
