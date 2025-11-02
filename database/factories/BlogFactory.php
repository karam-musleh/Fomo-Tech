<?php

namespace Database\Factories;

use App\Models\Mentor;
use App\Models\Section;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Blog>
 */
class BlogFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'title' => fake()->sentence(),
            'description' => fake()->paragraph(),
            'mentor_id' => Mentor::factory(), // مرتبط بمرشد
            'section_id' => Section::factory(), // مرتبط بسكشن
        ];
    }
}
