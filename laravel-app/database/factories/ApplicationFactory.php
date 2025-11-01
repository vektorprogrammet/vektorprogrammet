<?php

namespace Database\Factories;

use App\Models\Application;
use App\Models\User;
use App\Models\AdmissionPeriod;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Application>
 */
class ApplicationFactory extends Factory
{
    protected $model = Application::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'admission_period_id' => AdmissionPeriod::factory(),
            'user_id' => User::factory(),
            'year_of_study' => (string) $this->faker->numberBetween(1, 5),
            'monday' => $this->faker->boolean(),
            'tuesday' => $this->faker->boolean(),
            'wednesday' => $this->faker->boolean(),
            'thursday' => $this->faker->boolean(),
            'friday' => $this->faker->boolean(),
            'substitute' => false,
            'language' => $this->faker->randomElement(['Norwegian', 'English']),
            'double_position' => false,
            'previous_participation' => false,
            'team_interest' => false,
            'last_edited' => now(),
            'created' => now(),
            'heard_about_from' => [],
        ];
    }
}

