<?php

namespace Database\Factories;

use App\Models\Section;
use App\Models\Resource;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Track>
 */
class TrackFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
   public function definition(): array
    {
        return [
            'name' => fake()->unique()->sentence(2),
            'image' => fake()->imageUrl(640, 480, 'technology', true),
            'description' => fake()->paragraph(),
            'introduction' => fake()->sentence(),
            'content' => fake()->text(800),
            'section_id' => Section::factory(),
        ];
    }

    public function configure()
    {
        return $this->afterCreating(function ($track) {
            // إذا تريد ربط الموارد موجودة مسبقًا، يمكن حذف هذا الجزء
            $resources = Resource::factory(rand(2, 4))->create();
            $track->resources()->attach($resources->pluck('id')->toArray());
        });
    }
}
