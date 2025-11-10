<?php

use App\Models\User;
use Illuminate\Support\Facades\Notification;

describe('AuthController', function () {

    beforeEach(function () {
        Notification::fake();
    });

    describe('Registration', function () {
        it('can register a new user', function () {
            $response = $this->postJson('/api/auth/registration', [
                'name' => 'Test User',
                'email' => 'test@example.com',
            ]);

            $response->assertStatus(201)
                ->assertJson([
                    'message' => 'Успешная регистрация! Код доступа отправлен на почту!',
                ]);

            $this->assertDatabaseHas('users', [
                'email' => 'test@example.com',
                'name' => 'Test User',
            ]);
        });

        it('validates required fields for registration', function () {
            $response = $this->postJson('/api/auth/registration', []);

            $response->assertStatus(422)
                ->assertJsonValidationErrors(['name', 'email']);
        });

        it('validates email format', function () {
            $response = $this->postJson('/api/auth/registration', [
                'name' => 'Test User',
                'email' => 'invalid-email',
            ]);

            $response->assertStatus(422)
                ->assertJsonValidationErrors(['email']);
        });

        it('prevents duplicate email registration', function () {
            User::factory()->create(['email' => 'test@example.com']);

            $response = $this->postJson('/api/auth/registration', [
                'name' => 'Test User',
                'email' => 'test@example.com',
            ]);

            $response->assertStatus(422)
                ->assertJsonValidationErrors(['email']);
        });
    });

    describe('Get Code', function () {
        it('can request a two-factor code', function () {
            $user = User::factory()->create();

            $response = $this->postJson('/api/auth/get_code', [
                'email' => $user->email,
            ]);

            $response->assertStatus(201)
                ->assertJson([
                    'message' => 'Ваш код доступа отправлен на почту!',
                ]);

            $user->refresh();
            expect($user->two_factor_code)->not()->toBeNull();
            expect($user->two_factor_expires_at)->not()->toBeNull();
        });

        it('returns 422 for non-existent email', function () {
            $response = $this->postJson('/api/auth/get_code', [
                'email' => 'nonexistent@example.com',
            ]);

            $response->assertStatus(422)
                ->assertJsonValidationErrors(['email']);
        });

        it('validates email field', function () {
            $response = $this->postJson('/api/auth/get_code', []);

            $response->assertStatus(422)
                ->assertJsonValidationErrors(['email']);
        });
    });

    describe('Get Token', function () {
        it('can get token with valid code', function () {
            $user = User::factory()->create();
            $user->generateTwoFactorCode();

            $response = $this->postJson('/api/auth/get_token', [
                'email' => $user->email,
                'code' => $user->two_factor_code,
                'device_name' => 'Test Device',
            ]);

            $response->assertStatus(200);
            expect($response->getContent())->toBeString();

            $user->refresh();
            expect($user->tokens)->toHaveCount(1);
        });

        it('rejects invalid code', function () {
            $user = User::factory()->create();
            $user->generateTwoFactorCode();

            $response = $this->postJson('/api/auth/get_token', [
                'email' => $user->email,
                'code' => '000000',
                'device_name' => 'Test Device',
            ]);

            $response->assertStatus(422)
                ->assertJsonValidationErrors(['email']);
        });

        it('rejects expired code', function () {
            $user = User::factory()->create();
            $user->two_factor_code = '123456';
            $user->two_factor_expires_at = now()->subMinutes(15);
            $user->save();

            $response = $this->postJson('/api/auth/get_token', [
                'email' => $user->email,
                'code' => '123456',
                'device_name' => 'Test Device',
            ]);

            $response->assertStatus(422)
                ->assertJsonValidationErrors(['two_factor_expires_at']);
        });

        it('validates required fields', function () {
            $response = $this->postJson('/api/auth/get_token', []);

            $response->assertStatus(422)
                ->assertJsonValidationErrors(['email', 'code', 'device_name']);
        });
    });

    describe('Remove Tokens', function () {
        it('can remove all user tokens', function () {
            $user = User::factory()->create();
            $token = $user->createToken('Test Device')->plainTextToken;

            $response = $this->getJson('/api/auth/remove_tokens', [
                'Authorization' => 'Bearer ' . $token,
            ]);

            $response->assertStatus(200)
                ->assertJson([
                    'message' => 'Вы вышли на всех устройствах!',
                ]);

            $user->refresh();
            expect($user->tokens)->toHaveCount(0);
        });

        it('requires authentication', function () {
            $response = $this->getJson('/api/auth/remove_tokens');

            $response->assertStatus(401);
        });
    });
});
