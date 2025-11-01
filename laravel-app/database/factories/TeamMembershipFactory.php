<?php

namespace Database\Factories;

use App\Models\TeamMembership;
use App\Models\Team;
use App\Models\User;
use App\Models\Semester;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\TeamMembership>
 */
class TeamMembershipFactory extends Factory
{
    protected $model = TeamMembership::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'team_id' => Team::factory(),
            'user_id' => User::factory(),
            'start_semester_id' => Semester::factory(),
            'end_semester_id' => null, // Active by default
        ];
    }

    /**
     * Indicate that the membership is inactive (has end semester).
     */
    public function inactive(): static
    {
        return $this->state(fn (array $attributes) => [
            'end_semester_id' => Semester::factory(),
        ]);
    }
}

