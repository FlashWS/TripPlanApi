<?php

use App\Models\User;

describe('UserController', function () {

    describe('Show Profile', function () {
        it('can get authenticated user profile', function () {
            $user = User::factory()->create([
                'name' => 'Test User',
                'email' => 'test@example.com',
            ]);

            $response = $this->actingAs($user)
                ->getJson('/api/user');

            $response->assertStatus(200)
                ->assertJson([
                    'name' => 'Test User',
                    'email' => 'test@example.com',
                ]);
        });

        it('requires authentication', function () {
            $response = $this->getJson('/api/user');

            $response->assertStatus(401);
        });
    });

    describe('Update Profile', function () {
        it('can update user profile', function () {
            $user = User::factory()->create([
                'name' => 'Old Name',
                'email' => 'old@example.com',
            ]);

            $response = $this->actingAs($user)
                ->postJson('/api/user', [
                    'name' => 'New Name',
                    'email' => 'new@example.com',
                ]);

            $response->assertStatus(200)
                ->assertJson([
                    'name' => 'New Name',
                    'email' => 'new@example.com',
                ]);

            $this->assertDatabaseHas('users', [
                'id' => $user->id,
                'name' => 'New Name',
                'email' => 'new@example.com',
            ]);
        });

        it('validates required fields', function () {
            $user = User::factory()->create();

            $response = $this->actingAs($user)
                ->postJson('/api/user', []);

            $response->assertStatus(422)
                ->assertJsonValidationErrors(['name', 'email']);
        });

        it('validates email format', function () {
            $user = User::factory()->create();

            $response = $this->actingAs($user)
                ->postJson('/api/user', [
                    'name' => 'Test User',
                    'email' => 'invalid-email',
                ]);

            $response->assertStatus(422)
                ->assertJsonValidationErrors(['email']);
        });

        it('prevents duplicate email on update', function () {
            $user1 = User::factory()->create(['email' => 'user1@example.com']);
            $user2 = User::factory()->create(['email' => 'user2@example.com']);

            $response = $this->actingAs($user1)
                ->postJson('/api/user', [
                    'name' => 'Test User',
                    'email' => 'user2@example.com',
                ]);

            $response->assertStatus(422)
                ->assertJsonValidationErrors(['email']);
        });

        it('requires authentication', function () {
            $response = $this->postJson('/api/user', [
                'name' => 'Test User',
                'email' => 'test@example.com',
            ]);

            $response->assertStatus(401);
        });
    });
});
