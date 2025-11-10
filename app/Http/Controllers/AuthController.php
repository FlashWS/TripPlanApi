<?php

namespace App\Http\Controllers;

use App\DTO\CodeForm;
use App\DTO\MessageResponse;
use App\DTO\RegistrationForm;
use App\DTO\TokenForm;
use App\Http\Requests\CodeRequest;
use App\Http\Requests\RegistrationRequest;
use App\Http\Requests\TokenRequest;
use App\Models\User;
use App\Notifications\TwoFactorCode;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Validation\ValidationException;

/**
 * @group Авторизация
 */
class AuthController extends Controller
{
    /**
     * Регистрация нового пользователя
     *
     * Создает нового пользователя и отправляет код подтверждения на email.
     *
     * @response 200 {"message": "Успешная регистрация! Код доступа отправлен на почту!"}
     */
    public function registration(RegistrationRequest $request): MessageResponse
    {
        $registrationForm = RegistrationForm::from($request->validated());

        $user = User::query()->create($registrationForm->all());

        $user->generateTwoFactorCode();

        $user->notify(new TwoFactorCode());

        return new MessageResponse('Успешная регистрация! Код доступа отправлен на почту!');
    }

    /**
     * Запросить код доступа
     *
     * Отправляет код двухфакторной аутентификации на email пользователя.
     *
     * @response 200 {"message": "Ваш код доступа отправлен на почту!"}
     */
    public function getCode(CodeRequest $request): MessageResponse
    {
        $codeForm = CodeForm::from($request->validated());

        $user = User::query()->where('email', $codeForm->email)->firstOrFail();

        $user->generateTwoFactorCode();

        $user->notify(new TwoFactorCode());

        return new MessageResponse('Ваш код доступа отправлен на почту!');
    }

    /**
     * Получить токен доступа
     *
     * Обменивает код двухфакторной аутентификации на Bearer токен для API.
     *
     * @response 200 string "1|abcdefghijklmnopqrstuvwxyz"
     */
    public function getToken(TokenRequest $request): string
    {
        $tokenForm = TokenForm::from($request->validated());

        $user = User::query()->where('email', $tokenForm->email)->firstOrFail();

        if (! $user->two_factor_expires_at || $user->two_factor_expires_at->lt(now())) {
            $user->resetTwoFactorCode();
            throw ValidationException::withMessages([
                'two_factor_expires_at' => ['Срок действия двухфакторного кода истек. Пожалуйста, войдите еще раз.'],
            ]);
        }

        dump($user->two_factor_code, $tokenForm->code);

        if ($user->two_factor_code !== $tokenForm->code) {
            throw ValidationException::withMessages([
                'email' => ['Не верный двухфакторный код. Пожалуйста, войдите еще раз.'],
            ]);
        }

        $user->markEmailAsVerified();
        $user->resetTwoFactorCode();

        return $user->createToken($tokenForm->device_name)->plainTextToken;
    }

    /**
     * Выйти на всех устройствах
     *
     * Удаляет все токены доступа пользователя, выполняя выход на всех устройствах.
     *
     * @authenticated
     * @response 200 {"message": "Вы вышли на всех устройствах!"}
     * @throws AuthenticationException
     */
    public function removeTokens(): MessageResponse
    {
        if(!$user = auth()->user()) {
            throw new AuthenticationException('Пользователь не авторизован!');
        }

        $user->tokens()->delete();

        return new MessageResponse('Вы вышли на всех устройствах!');
    }
}
