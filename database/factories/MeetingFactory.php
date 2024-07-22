<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Carbon;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Meeting>
 */
class MeetingFactory extends Factory
{
    public function definition(): array
    {
        $startsAt = Carbon::make($this->faker->dateTimeBetween('now', '+1 week'))
            ->setMinutes($this->faker->randomElement([0, 30]))
        ;
        $endsAt = $startsAt->clone()->addHours($this->faker->numberBetween(1, 3));

        return [
            'title' => $this->faker->sentence(3),
            'description' => $this->faker->paragraph,
            'starts_at' => $startsAt,
            'ends_at' => $endsAt,
        ];
    }
}
