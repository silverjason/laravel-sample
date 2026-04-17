<?php

namespace App\Modules\Projects\Database\Factories;

use App\Models\User;
use App\Modules\Projects\Enums\ProjectPriority;
use App\Modules\Projects\Enums\ProjectStatus;
use App\Modules\Projects\Models\Project;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Project>
 */
class ProjectFactory extends Factory
{
    protected $model = Project::class;

    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'name' => fake()->sentence(3),
            'description' => fake()->optional()->paragraph(),
            'status' => ProjectStatus::Draft,
            'priority' => fake()->randomElement(ProjectPriority::cases()),
            'due_date' => fake()->optional()->dateTimeBetween('now', '+1 month'),
            'started_at' => null,
            'completed_at' => null,
        ];
    }

    public function active(): static
    {
        return $this->state(fn (): array => [
            'status' => ProjectStatus::Active,
            'started_at' => now(),
        ]);
    }

    public function completed(): static
    {
        return $this->state(fn (): array => [
            'status' => ProjectStatus::Completed,
            'started_at' => now()->subDay(),
            'completed_at' => now(),
        ]);
    }

    public function archived(): static
    {
        return $this->state(fn (): array => [
            'status' => ProjectStatus::Archived,
        ]);
    }
}
