<?php

use App\Models\Point;
use App\Models\Trip;
use App\Models\TripPoint;
use App\Models\User;

describe('TripPointController', function () {

    beforeEach(function () {
        $this->user = User::factory()->create();
        $this->trip = Trip::factory()->create(['user_id' => $this->user->id]);
    });

    describe('Index', function () {
        it('can list trip points', function () {
            $point1 = Point::factory()->create(['user_id' => $this->user->id]);
            $point2 = Point::factory()->create(['user_id' => $this->user->id]);

            TripPoint::factory()->create([
                'trip_uuid' => $this->trip->uuid,
                'point_uuid' => $point1->uuid,
                'day' => 1,
                'order' => 1,
            ]);

            TripPoint::factory()->create([
                'trip_uuid' => $this->trip->uuid,
                'point_uuid' => $point2->uuid,
                'day' => 1,
                'order' => 2,
            ]);

            $response = $this->actingAs($this->user)
                ->getJson("/api/trips/{$this->trip->uuid}/points");

            $response->assertStatus(200)
                ->assertJsonCount(2);
        });

        it('returns points ordered by day and order', function () {
            $point1 = Point::factory()->create(['user_id' => $this->user->id, 'name' => 'Point 1']);
            $point2 = Point::factory()->create(['user_id' => $this->user->id, 'name' => 'Point 2']);
            $point3 = Point::factory()->create(['user_id' => $this->user->id, 'name' => 'Point 3']);

            TripPoint::factory()->create([
                'trip_uuid' => $this->trip->uuid,
                'point_uuid' => $point3->uuid,
                'day' => 2,
                'order' => 1,
            ]);

            TripPoint::factory()->create([
                'trip_uuid' => $this->trip->uuid,
                'point_uuid' => $point1->uuid,
                'day' => 1,
                'order' => 1,
            ]);

            TripPoint::factory()->create([
                'trip_uuid' => $this->trip->uuid,
                'point_uuid' => $point2->uuid,
                'day' => 1,
                'order' => 2,
            ]);

            $response = $this->actingAs($this->user)
                ->getJson("/api/trips/{$this->trip->uuid}/points");

            $response->assertStatus(200);

            $data = $response->json();
            expect($data[0]['day'])->toBe(1);
            expect($data[0]['order'])->toBe(1);
            expect($data[1]['day'])->toBe(1);
            expect($data[1]['order'])->toBe(2);
            expect($data[2]['day'])->toBe(2);
        });

        it('requires authentication', function () {
            $response = $this->getJson("/api/trips/{$this->trip->uuid}/points");

            $response->assertStatus(401);
        });
    });

    describe('Store', function () {
        it('can add a point to trip', function () {
            $point = Point::factory()->create(['user_id' => $this->user->id]);

            $data = [
                'point_uuid' => $point->uuid,
                'day' => 1,
                'time' => '10:00',
                'order' => 1,
                'note' => 'Visit in the morning',
            ];

            $response = $this->actingAs($this->user)
                ->postJson("/api/trips/{$this->trip->uuid}/points", $data);

            $response->assertStatus(201)
                ->assertJsonFragment([
                    'point_uuid' => $point->uuid,
                    'day' => 1,
                    'time' => '10:00',
                    'order' => 1,
                ]);

            $this->assertDatabaseHas('trip_point', [
                'trip_uuid' => $this->trip->uuid,
                'point_uuid' => $point->uuid,
                'day' => 1,
            ]);
        });

        it('validates required fields', function () {
            $response = $this->actingAs($this->user)
                ->postJson("/api/trips/{$this->trip->uuid}/points", []);

            $response->assertStatus(422)
                ->assertJsonValidationErrors(['point_uuid', 'day']);
        });

        it('validates point exists', function () {
            $data = [
                'point_uuid' => 'non-existent-uuid',
                'day' => 1,
            ];

            $response = $this->actingAs($this->user)
                ->postJson("/api/trips/{$this->trip->uuid}/points", $data);

            $response->assertStatus(422)
                ->assertJsonValidationErrors(['point_uuid']);
        });

        it('validates time format', function () {
            $point = Point::factory()->create(['user_id' => $this->user->id]);

            $data = [
                'point_uuid' => $point->uuid,
                'day' => 1,
                'time' => 'invalid-time',
            ];

            $response = $this->actingAs($this->user)
                ->postJson("/api/trips/{$this->trip->uuid}/points", $data);

            $response->assertStatus(422)
                ->assertJsonValidationErrors(['time']);
        });

        it('requires authentication', function () {
            $point = Point::factory()->create();

            $response = $this->postJson("/api/trips/{$this->trip->uuid}/points", [
                'point_uuid' => $point->uuid,
                'day' => 1,
            ]);

            $response->assertStatus(401);
        });
    });

    describe('Show', function () {
        it('can show a trip point', function () {
            $point = Point::factory()->create(['user_id' => $this->user->id]);
            $tripPoint = TripPoint::factory()->create([
                'trip_uuid' => $this->trip->uuid,
                'point_uuid' => $point->uuid,
            ]);

            $response = $this->actingAs($this->user)
                ->getJson("/api/trips/{$this->trip->uuid}/points/{$tripPoint->uuid}");

            $response->assertStatus(200)
                ->assertJson([
                    'uuid' => $tripPoint->uuid,
                    'trip_uuid' => $this->trip->uuid,
                    'point_uuid' => $point->uuid,
                ]);
        });

        it('requires authentication', function () {
            $tripPoint = TripPoint::factory()->create([
                'trip_uuid' => $this->trip->uuid,
            ]);

            $response = $this->getJson("/api/trips/{$this->trip->uuid}/points/{$tripPoint->uuid}");

            $response->assertStatus(401);
        });
    });

    describe('Update', function () {
        it('can update a trip point', function () {
            $point1 = Point::factory()->create(['user_id' => $this->user->id]);
            $point2 = Point::factory()->create(['user_id' => $this->user->id]);
            $tripPoint = TripPoint::factory()->create([
                'trip_uuid' => $this->trip->uuid,
                'point_uuid' => $point1->uuid,
                'day' => 1,
                'order' => 1,
            ]);

            $data = [
                'point_uuid' => $point2->uuid,
                'day' => 2,
                'time' => '15:00',
                'order' => 2,
                'note' => 'Updated note',
            ];

            $response = $this->actingAs($this->user)
                ->putJson("/api/trips/{$this->trip->uuid}/points/{$tripPoint->uuid}", $data);

            $response->assertStatus(200)
                ->assertJson([
                    'point_uuid' => $point2->uuid,
                    'day' => 2,
                    'time' => '15:00',
                    'order' => 2,
                ]);

            $this->assertDatabaseHas('trip_point', [
                'uuid' => $tripPoint->uuid,
                'point_uuid' => $point2->uuid,
                'day' => 2,
            ]);
        });

        it('validates required fields', function () {
            $tripPoint = TripPoint::factory()->create([
                'trip_uuid' => $this->trip->uuid,
            ]);

            $response = $this->actingAs($this->user)
                ->putJson("/api/trips/{$this->trip->uuid}/points/{$tripPoint->uuid}", []);

            $response->assertStatus(422)
                ->assertJsonValidationErrors(['point_uuid', 'day']);
        });

        it('requires authentication', function () {
            $tripPoint = TripPoint::factory()->create([
                'trip_uuid' => $this->trip->uuid,
            ]);

            $response = $this->putJson("/api/trips/{$this->trip->uuid}/points/{$tripPoint->uuid}", [
                'day' => 2,
            ]);

            $response->assertStatus(401);
        });
    });

    describe('Destroy', function () {
        it('can delete a trip point', function () {
            $point = Point::factory()->create(['user_id' => $this->user->id]);
            $tripPoint = TripPoint::factory()->create([
                'trip_uuid' => $this->trip->uuid,
                'point_uuid' => $point->uuid,
            ]);

            $response = $this->actingAs($this->user)
                ->deleteJson("/api/trips/{$this->trip->uuid}/points/{$tripPoint->uuid}");

            $response->assertStatus(204);

            $this->assertDatabaseMissing('trip_point', [
                'uuid' => $tripPoint->uuid,
            ]);

            // Point itself should still exist
            $this->assertDatabaseHas('points', [
                'uuid' => $point->uuid,
            ]);
        });

        it('requires authentication', function () {
            $tripPoint = TripPoint::factory()->create([
                'trip_uuid' => $this->trip->uuid,
            ]);

            $response = $this->deleteJson("/api/trips/{$this->trip->uuid}/points/{$tripPoint->uuid}");

            $response->assertStatus(401);
        });
    });
});
