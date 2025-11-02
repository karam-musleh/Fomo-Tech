<?php

namespace Database\Factories;

use App\Models\Mentor;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Resource>
 */
class ResourceFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->sentence(3), // اسم الكورس أو الكتاب أو الأداة
            'link' => fake()->url(),
            'type' => fake()->randomElement(['Course', 'Book', 'Tool']),
            'mentor_id' => Mentor::factory(), //  سيقوم بإنشاء Mentor جديد لكل Resource

        ];
    }
}
