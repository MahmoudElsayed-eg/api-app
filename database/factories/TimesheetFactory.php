<?php

namespace Database\Factories;

use App\Models\Project;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Timesheet>
 */
class TimesheetFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'task_name' => fake()->word() . ' task',
            'date' => fake()->date(),
            'hours' => fake()->numberBetween(),
            'user_id' => User::inRandomOrder()->value('id') ?? User::factory(),
            'project_id' => Project::inRandomOrder()->value('id') ?? Project::factory(),
        ];
    }
}
