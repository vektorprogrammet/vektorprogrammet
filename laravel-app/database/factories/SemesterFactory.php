<?php

namespace Database\Factories;

use App\Models\Semester;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Semester>
 */
class SemesterFactory extends Factory
{
    protected $model = Semester::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $year = (string) $this->faker->year();
        $semesterTime = $this->faker->randomElement(['Vår', 'Høst']);

        return [
            'semester_time' => $semesterTime,
            'year' => $year,
        ];
    }
}

