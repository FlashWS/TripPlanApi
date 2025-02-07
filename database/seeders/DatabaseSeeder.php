<?php

namespace Database\Seeders;

use App\Models\Point;
use App\Models\User;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        User::factory(10)
            ->has(Point::factory()->count(30))
            ->create();

        User::factory()
            ->has(Point::factory()->count(30))
            ->create([
                'name' => 'Lisin Sergey',
                'email' => 'sergey@lisin.expert',
            ]);
    }
}
