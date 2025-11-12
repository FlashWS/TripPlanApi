<?php

use App\Models\Point;
use App\Models\Trip;
use App\Models\TripPoint;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

describe('TripPoint Time Format', function () {
    it('formats time to H:i format when saving and retrieving', function () {
        $user = User::factory()->create();
        $this->actingAs($user);

        $trip = Trip::factory()->create(['user_id' => $user->id]);
        $point = Point::factory()->create(['user_id' => $user->id]);

        // Создаем TripPoint с временем в формате H:i:s
        $tripPoint = TripPoint::create([
            'user_id' => $user->id,
            'trip_uuid' => $trip->uuid,
            'point_uuid' => $point->uuid,
            'day' => 1,
            'time' => '14:30:45',
            'order' => 1,
        ]);

        // Перезагружаем из базы и проверяем, что формат H:i
        $tripPoint->refresh();
        expect($tripPoint->time)->toBe('14:30');
    });

    it('formats various time formats to H:i', function () {
        $user = User::factory()->create();
        $this->actingAs($user);

        $trip = Trip::factory()->create(['user_id' => $user->id]);
        $point = Point::factory()->create(['user_id' => $user->id]);

        $testCases = [
            ['input' => '09:15:30', 'expected' => '09:15'],
            ['input' => '00:00:00', 'expected' => '00:00'],
            ['input' => '12:00:00', 'expected' => '12:00'],
            ['input' => '23:59:59', 'expected' => '23:59'],
            ['input' => '16:45', 'expected' => '16:45'], // Уже в формате H:i
        ];

        foreach ($testCases as $testCase) {
            $tripPoint = TripPoint::create([
                'user_id' => $user->id,
                'trip_uuid' => $trip->uuid,
                'point_uuid' => $point->uuid,
                'day' => 1,
                'time' => $testCase['input'],
                'order' => 1,
            ]);

            $tripPoint->refresh();
            expect($tripPoint->time)->toBe($testCase['expected']);

            // Cleanup
            $tripPoint->delete();
        }
    });

    it('handles null time values', function () {
        $user = User::factory()->create();
        $this->actingAs($user);

        $trip = Trip::factory()->create(['user_id' => $user->id]);
        $point = Point::factory()->create(['user_id' => $user->id]);

        $tripPoint = TripPoint::create([
            'user_id' => $user->id,
            'trip_uuid' => $trip->uuid,
            'point_uuid' => $point->uuid,
            'day' => 1,
            'time' => null,
            'order' => 1,
        ]);

        expect($tripPoint->time)->toBeNull();
    });

    it('returns time in H:i format via API', function () {
        $user = User::factory()->create();

        $trip = Trip::factory()->create(['user_id' => $user->id]);
        $point = Point::factory()->create(['user_id' => $user->id]);

        // Создаем через API с временем в правильном формате
        $response = $this->actingAs($user)
            ->postJson("/api/trips/{$trip->uuid}/points", [
                'point_uuid' => $point->uuid,
                'day' => 1,
                'time' => '14:30', // Отправляем в формате H:i
                'order' => 1,
            ]);

        $response->assertStatus(201)
            ->assertJsonFragment([
                'time' => '14:30', // Ожидаем формат H:i
            ]);
    });
});
