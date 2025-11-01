<?php

namespace Database\Factories;

use App\Models\Team;
use App\Models\Department;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Team>
 */
class TeamFactory extends Factory
{
    protected $model = Team::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => $this->faker->words(2, true),
            'email' => $this->faker->unique()->safeEmail(),
            'department_id' => Department::factory(),
            'active' => true,
            'accept_application' => true,
        ];
    }
}

