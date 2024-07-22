<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Carbon;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Task>
 */
class TaskFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $startsAt = Carbon::make($this->faker->dateTimeBetween('now', '+1 week'))
            ->setMinutes(0)
            ->setHour($this->faker->numberBetween(8, 14))
        ;
        $endsAt = $startsAt->clone()->addHours(2);

        return [
            'title' => $this->faker->sentence(3, false),
            'description' => $this->faker->paragraph(),
            'starts_at' => $startsAt,
            'ends_at' => $endsAt,
        ];
    }
}
