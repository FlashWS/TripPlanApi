<?php

namespace Database\Seeders;

use App\Models\Point;
use App\Models\Tag;
use App\Models\Trip;
use App\Models\TripPoint;
use App\Models\User;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Auth;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $admin = User::factory()
            ->create([
                'name' => 'Сергей',
                'email' => 'sergey@lisin.expert',
            ]);
        $users = User::factory(10)->create();

        $users->add($admin);

        foreach ($users as $user) {
            Auth::loginUsingId($user->id);

            $tags = Tag::factory(5)->create([
                'user_id' => $user->id,
            ]);

            Point::factory(10)
                ->hasAttached($tags)
                ->create([
                    'user_id' => $user->id,
                ]);

            $trips = Trip::factory(5)->create([
                'user_id' => $user->id,
            ]);

            foreach ($trips as $trip) {
                TripPoint::factory()->create([
                    'user_id' => $user->id,
                    'trip_uuid' => $trip->uuid,
                    'point_uuid' => $user->points()->inRandomOrder()->first()->uuid,
                ]);
            }
        }
    }
}
