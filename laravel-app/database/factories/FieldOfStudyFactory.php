<?php

namespace Database\Factories;

use App\Models\FieldOfStudy;
use App\Models\Department;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\FieldOfStudy>
 */
class FieldOfStudyFactory extends Factory
{
    protected $model = FieldOfStudy::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => $this->faker->words(3, true),
            'short_name' => strtoupper($this->faker->unique()->lexify('???')),
            'department_id' => Department::factory(),
        ];
    }
}

