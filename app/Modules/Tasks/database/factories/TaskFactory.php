<?php

namespace App\Modules\Tasks\Database\Factories;

use App\Modules\Projects\Models\Project;
use App\Modules\Tasks\Enums\TaskStatus;
use App\Modules\Tasks\Models\Task;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Task>
 */
class TaskFactory extends Factory
{
    protected $model = Task::class;

    public function definition(): array
    {
        return [
            'project_id' => Project::factory(),
            'name' => fake()->sentence(4),
            'status' => TaskStatus::Pending,
            'due_date' => fake()->optional()->dateTimeBetween('now', '+1 month'),
            'completed_at' => null,
        ];
    }

    public function inProgress(): static
    {
        return $this->state(fn (): array => [
            'status' => TaskStatus::InProgress,
        ]);
    }

    public function completed(): static
    {
        return $this->state(fn (): array => [
            'status' => TaskStatus::Completed,
            'completed_at' => now(),
        ]);
    }

    public function blocked(): static
    {
        return $this->state(fn (): array => [
            'status' => TaskStatus::Blocked,
        ]);
    }
}
