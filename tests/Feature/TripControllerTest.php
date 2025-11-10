<?php

use App\Models\Trip;
use App\Models\User;

describe('TripController', function () {

    beforeEach(function () {
        $this->user = User::factory()->create();
    });

    describe('Index', function () {
        it('can list user trips', function () {
            Trip::factory()->count(3)->create(['user_id' => $this->user->id]);
            Trip::factory()->create(); // Another user's trip

            $response = $this->actingAs($this->user)
                ->getJson('/api/trips');

            $response->assertStatus(200)
                ->assertJsonCount(3, 'data');
        });

        it('returns paginated results', function () {
            Trip::factory()->count(20)->create(['user_id' => $this->user->id]);

            $response = $this->actingAs($this->user)
                ->getJson('/api/trips');

            $response->assertStatus(200)
                ->assertJsonStructure([
                    'data',
                    'links',
                    'meta',
                ]);
        });

        it('requires authentication', function () {
            $response = $this->getJson('/api/trips');

            $response->assertStatus(401);
        });
    });

    describe('Store', function () {
        it('can create a new trip', function () {
            $data = [
                'name' => 'Test Trip',
                'date_start' => '2025-01-01',
                'date_end' => '2025-01-10',
                'note' => 'Test note',
            ];

            $response = $this->actingAs($this->user)
                ->postJson('/api/trips', $data);

            $response->assertStatus(200)
                ->assertJsonFragment([
                    'name' => 'Test Trip',
                ]);

            $this->assertDatabaseHas('trips', [
                'name' => 'Test Trip',
                'user_id' => $this->user->id,
            ]);
        });

        it('validates required fields', function () {
            $response = $this->actingAs($this->user)
                ->postJson('/api/trips', []);

            $response->assertStatus(422)
                ->assertJsonValidationErrors(['name', 'date_start', 'date_end']);
        });

        it('validates date format', function () {
            $response = $this->actingAs($this->user)
                ->postJson('/api/trips', [
                    'name' => 'Test Trip',
                    'date_start' => 'invalid-date',
                    'date_end' => 'invalid-date',
                ]);

            $response->assertStatus(422)
                ->assertJsonValidationErrors(['date_start', 'date_end']);
        });

        it('requires authentication', function () {
            $response = $this->postJson('/api/trips', [
                'name' => 'Test Trip',
                'date_start' => '2025-01-01',
                'date_end' => '2025-01-10',
            ]);

            $response->assertStatus(401);
        });
    });

    describe('Show', function () {
        it('can show a specific trip', function () {
            $trip = Trip::factory()->create(['user_id' => $this->user->id]);

            $response = $this->actingAs($this->user)
                ->getJson("/api/trips/{$trip->uuid}");

            $response->assertStatus(200)
                ->assertJson([
                    'uuid' => $trip->uuid,
                    'name' => $trip->name,
                ]);
        });

        it('cannot show another user trip', function () {
            $otherUser = User::factory()->create();
            $trip = Trip::factory()->create(['user_id' => $otherUser->id]);

            $response = $this->actingAs($this->user)
                ->getJson("/api/trips/{$trip->uuid}");

            $response->assertStatus(404);
        });

        it('returns 404 for non-existent trip', function () {
            $response = $this->actingAs($this->user)
                ->getJson('/api/trips/non-existent-uuid');

            $response->assertStatus(404);
        });

        it('requires authentication', function () {
            $trip = Trip::factory()->create();

            $response = $this->getJson("/api/trips/{$trip->uuid}");

            $response->assertStatus(401);
        });
    });

    describe('Update', function () {
        it('can update a trip', function () {
            $trip = Trip::factory()->create(['user_id' => $this->user->id]);

            $data = [
                'name' => 'Updated Trip',
                'date_start' => '2025-02-01',
                'date_end' => '2025-02-10',
                'note' => 'Updated note',
            ];

            $response = $this->actingAs($this->user)
                ->putJson("/api/trips/{$trip->uuid}", $data);

            $response->assertStatus(200)
                ->assertJson([
                    'name' => 'Updated Trip',
                ]);

            $this->assertDatabaseHas('trips', [
                'uuid' => $trip->uuid,
                'name' => 'Updated Trip',
            ]);
        });

        it('cannot update another user trip', function () {
            $otherUser = User::factory()->create();
            $trip = Trip::factory()->create(['user_id' => $otherUser->id]);

            $response = $this->actingAs($this->user)
                ->putJson("/api/trips/{$trip->uuid}", [
                    'name' => 'Updated Trip',
                    'date_start' => '2025-02-01',
                    'date_end' => '2025-02-10',
                ]);

            $response->assertStatus(404);
        });

        it('validates required fields', function () {
            $trip = Trip::factory()->create(['user_id' => $this->user->id]);

            $response = $this->actingAs($this->user)
                ->putJson("/api/trips/{$trip->uuid}", []);

            $response->assertStatus(422)
                ->assertJsonValidationErrors(['name', 'date_start', 'date_end']);
        });

        it('requires authentication', function () {
            $trip = Trip::factory()->create();

            $response = $this->putJson("/api/trips/{$trip->uuid}", [
                'name' => 'Updated Trip',
            ]);

            $response->assertStatus(401);
        });
    });

    describe('Destroy', function () {
        it('can delete a trip', function () {
            $trip = Trip::factory()->create(['user_id' => $this->user->id]);

            $response = $this->actingAs($this->user)
                ->deleteJson("/api/trips/{$trip->uuid}");

            $response->assertStatus(204);

            $this->assertDatabaseMissing('trips', [
                'uuid' => $trip->uuid,
            ]);
        });

        it('cannot delete another user trip', function () {
            $otherUser = User::factory()->create();
            $trip = Trip::factory()->create(['user_id' => $otherUser->id]);

            $response = $this->actingAs($this->user)
                ->deleteJson("/api/trips/{$trip->uuid}");

            $response->assertStatus(404);

            $this->assertDatabaseHas('trips', [
                'uuid' => $trip->uuid,
            ]);
        });

        it('requires authentication', function () {
            $trip = Trip::factory()->create();

            $response = $this->deleteJson("/api/trips/{$trip->uuid}");

            $response->assertStatus(401);
        });
    });
});
