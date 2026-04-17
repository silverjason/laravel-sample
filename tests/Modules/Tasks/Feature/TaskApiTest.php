<?php

namespace Tests\Modules\Tasks\Feature;

use App\Models\User;
use App\Modules\Projects\Enums\ProjectPriority;
use App\Modules\Projects\Enums\ProjectStatus;
use App\Modules\Projects\Models\Project;
use App\Modules\Tasks\Enums\TaskStatus;
use App\Modules\Tasks\Models\Task;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TaskApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_guests_cannot_access_task_routes(): void
    {
        // Act
        $response = $this->getJson('/api/tasks');

        // Assert
        $response->assertUnauthorized();
    }

    public function test_authenticated_users_can_list_tasks_with_project_data(): void
    {
        // Arrange
        $user = User::factory()->create();
        $project = Project::factory()->create(['name' => 'Alpha Launch']);
        Task::factory()->create(['project_id' => $project->id, 'name' => 'Draft kickoff plan']);
        Task::factory()->create(['project_id' => $project->id, 'name' => 'Confirm team owners']);
        Task::factory()->create(['project_id' => $project->id, 'name' => 'Beta task']);

        // Act
        $response = $this
            ->actingAs($user)
            ->getJson('/api/tasks?search=Draft');

        // Assert
        $response
            ->assertOk()
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('data.0.name', 'Draft kickoff plan')
            ->assertJsonPath('data.0.project.name', 'Alpha Launch');
    }

    public function test_authenticated_users_can_create_update_complete_and_delete_tasks(): void
    {
        // Arrange
        $user = User::factory()->create();
        $project = $user->projects()->create([
            'name' => 'Task Test Project',
            'description' => 'Project for task coverage.',
            'status' => ProjectStatus::Draft,
            'priority' => ProjectPriority::Medium,
        ]);

        // Act
        $createdResponse = $this->actingAs($user)->postJson('/api/tasks', [
            'project_id' => $project->id,
            'name' => 'Write kickoff brief',
            'status' => TaskStatus::Pending->value,
            'due_date' => now()->addWeek()->toISOString(),
        ]);

        // Assert
        $createdResponse
            ->assertCreated()
            ->assertJsonPath('data.name', 'Write kickoff brief')
            ->assertJsonPath('data.status', TaskStatus::Pending->value)
            ->assertJsonPath('data.project.name', $project->name);

        $task = Task::query()->firstOrFail();

        // Act
        $updatedResponse = $this->actingAs($user)->patchJson("/api/tasks/{$task->id}", [
            'status' => TaskStatus::InProgress->value,
        ]);

        // Assert
        $updatedResponse
            ->assertOk()
            ->assertJsonPath('data.status', TaskStatus::InProgress->value);

        // Act
        $completedResponse = $this->actingAs($user)->postJson("/api/tasks/{$task->id}/complete");

        // Assert
        $completedResponse
            ->assertOk()
            ->assertJsonPath('data.status', TaskStatus::Completed->value)
            ->assertJsonPath('data.completed_at', fn (?string $value) => filled($value));

        // Act
        $deletedResponse = $this->actingAs($user)
            ->deleteJson("/api/tasks/{$task->id}");

        // Assert
        $deletedResponse->assertNoContent();
        $this->assertSoftDeleted($task);
    }

    public function test_blocked_tasks_cannot_be_completed(): void
    {
        // Arrange
        $user = User::factory()->create();
        $project = $user->projects()->create([
            'name' => 'Blocked Task Project',
            'description' => 'Project for blocked task coverage.',
            'status' => ProjectStatus::Draft,
            'priority' => ProjectPriority::Medium,
        ]);
        $task = Task::factory()->blocked()->create([
            'project_id' => $project->id,
        ]);

        // Act
        $response = $this->actingAs($user)
            ->postJson("/api/tasks/{$task->id}/complete");

        // Assert
        $response
            ->assertUnprocessable()
            ->assertJsonValidationErrors(['status']);
    }
}
