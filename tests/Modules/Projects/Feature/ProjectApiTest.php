<?php

namespace Tests\Modules\Projects\Feature;

use App\Models\User;
use App\Modules\Projects\Enums\ProjectPriority;
use App\Modules\Projects\Enums\ProjectStatus;
use App\Modules\Projects\Models\Project;
use App\Modules\Tasks\Models\Task;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProjectApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_guests_cannot_access_project_routes(): void
    {
        // Act
        $response = $this->getJson('/api/projects');

        // Assert
        $response->assertUnauthorized();
    }

    public function test_authenticated_users_can_list_projects_with_task_counts(): void
    {
        // Arrange
        $user = User::factory()->create();
        $matchingProject = Project::factory()->create(['name' => 'Alpha Launch']);
        $otherProject = Project::factory()->create(['name' => 'Beta Migration']);

        Task::query()->create([
            'project_id' => $matchingProject->id,
            'name' => 'Draft kickoff plan',
            'status' => 'pending',
        ]);

        Task::query()->create([
            'project_id' => $matchingProject->id,
            'name' => 'Confirm team owners',
            'status' => 'pending',
        ]);

        // Act
        $response = $this
            ->actingAs($user)
            ->getJson('/api/projects?search=Alpha');

        // Assert
        $response
            ->assertOk()
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('data.0.name', 'Alpha Launch')
            ->assertJsonPath('data.0.tasks_count', 2);

        $this->assertNotSame($matchingProject->id, $otherProject->id);
    }

    public function test_authenticated_users_can_create_update_complete_and_delete_projects(): void
    {
        // Arrange
        $user = User::factory()->create();

        // Act
        $createdResponse = $this->actingAs($user)->postJson('/api/projects', [
            'name' => 'Platform Refresh',
            'description' => 'Refresh the internal project platform.',
            'status' => ProjectStatus::Draft->value,
            'priority' => ProjectPriority::High->value,
            'due_date' => now()->addWeek()->toISOString(),
        ]);

        // Assert
        $createdResponse
            ->assertCreated()
            ->assertJsonPath('data.name', 'Platform Refresh')
            ->assertJsonPath('data.status', ProjectStatus::Draft->value)
            ->assertJsonPath('data.priority', ProjectPriority::High->value)
            ->assertJsonPath('data.tasks_count', 0);

        $project = Project::query()->firstOrFail();

        // Act
        $updatedResponse = $this->actingAs($user)->patchJson("/api/projects/{$project->id}", [
            'status' => ProjectStatus::Active->value,
            'started_at' => now()->subHour()->toISOString(),
        ]);

        // Assert
        $updatedResponse
            ->assertOk()
            ->assertJsonPath('data.status', ProjectStatus::Active->value);

        // Act
        $completedResponse = $this->actingAs($user)->postJson("/api/projects/{$project->id}/complete");

        // Assert
        $completedResponse
            ->assertOk()
            ->assertJsonPath('data.status', ProjectStatus::Completed->value)
            ->assertJsonPath('data.completed_at', fn (?string $value) => filled($value));

        // Act
        $deletedResponse = $this->actingAs($user)
            ->deleteJson("/api/projects/{$project->id}");

        // Assert
        $deletedResponse->assertNoContent();
        $this->assertSoftDeleted($project);
    }

    public function test_archived_projects_cannot_be_completed(): void
    {
        // Arrange
        $user = User::factory()->create();
        $project = Project::factory()->archived()->create();

        // Act
        $response = $this->actingAs($user)
            ->postJson("/api/projects/{$project->id}/complete");

        // Assert
        $response
            ->assertUnprocessable()
            ->assertJsonValidationErrors(['status']);
    }
}
