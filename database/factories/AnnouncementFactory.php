<?php

namespace Database\Factories;

use App\Models\Announcement;
use Illuminate\Database\Eloquent\Factories\Factory;

class AnnouncementFactory extends Factory
{
    protected $model = Announcement::class;

    public function definition(): array
    {
        $priorities = ['high', 'medium', 'low'];

        return [
            'title' => $this->faker->bs, // Short, catchy phrase
            'content' => $this->faker->realText(500),
            'priority' => $this->faker->randomElement($priorities),
            'published_at' => $this->faker->dateTimeBetween('-3 months', '+1 month'), // Some past, some future
        ];
    }
}
