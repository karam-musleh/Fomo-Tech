<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\User>
 */
class UserFactory extends Factory
{
    /**
     * The current password being used by the factory.
     */
    protected static ?string $password;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'first_name' => fake()->firstName(),
            'last_name' => fake()->lastName(),
            'email' => fake()->unique()->safeEmail(),
            'role' => fake()->randomElement(['admin', 'student', 'mentor']),
            'date_of_birth' => fake()->date(),
            'pronoun' => fake()->randomElement(['he/him', 'she/her', 'they/them']),
            'major' => fake()->word(),
            'profile_photo' => fake()->imageUrl(200, 200, 'people'),
            'goals' => fake()->sentence(),
            'bio' => fake()->paragraph(),
            'linkedin_url' => 'https://linkedin.com/in/' . fake()->userName(),
            'email_verified_at' => now(),
            'password' => bcrypt('password'), // تقدر تغيرها
            'otp_code' => null,
            'otp_expires_at' => null,
            'remember_token' => Str::random(10),
        ];
        //     return [
        //         'name' => fake()->name(),
        //         'email' => fake()->unique()->safeEmail(),
        //         'email_verified_at' => now(),
        //         'password' => static::$password ??= Hash::make('password'),
        //         'remember_token' => Str::random(10),
        //     ];
    }

    /**
     * Indicate that the model's email address should be unverified.
     */
    public function unverified(): static
    {
        return $this->state(fn(array $attributes) => [
            'email_verified_at' => null,
        ]);
    }
}
