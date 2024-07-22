<?php

namespace Database\Seeders;

use App\Models\Meeting;
use App\Models\Sprint;
use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Collection;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {

        Collection::times(5, function () {
            Meeting::factory()
                ->hasUsers(random_int(1, 3))
                ->create()
            ;
        });
        Sprint::factory(2)->create();

        User::factory()->create([
            'name' => 'Test User',
            'email' => 'test@example.com',
        ]);
    }
}
