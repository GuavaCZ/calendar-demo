<?php

namespace Database\Factories;

use App\Enums\Priority;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Carbon;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Sprint>
 */
class SprintFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $startsAt = Carbon::make($this->faker->dateTimeBetween('now', '+2 week'));
        $endsAt = $startsAt->clone()->addWeek();
        return [
            'title' => $this->faker->sentence,
            'description' => $this->faker->paragraph,
            'priority' => $this->faker->randomElement(Priority::cases()),
            'starts_at' => $startsAt,
            'ends_at' => $endsAt
        ];
    }
}
