<?php

namespace Database\Factories;

use App\Models\AdmissionPeriod;
use App\Models\Department;
use App\Models\Semester;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\AdmissionPeriod>
 */
class AdmissionPeriodFactory extends Factory
{
    protected $model = AdmissionPeriod::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $startDate = $this->faker->dateTimeBetween('-1 year', '+1 year');
        $endDate = (clone $startDate)->modify('+3 months');

        return [
            'department_id' => Department::factory(),
            'semester_id' => Semester::factory(),
            'start_date' => $startDate,
            'end_date' => $endDate,
        ];
    }
}

