<?php

namespace Tests\Modules\Projects\Feature;

use App\Modules\Projects\Models\Project;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DispatchProjectSnapshotCommandTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_dispatches_the_project_snapshot_job(): void
    {
        // Arrange
        $project = Project::factory()->create();

        // Act
        $command = $this->artisan('projects:snapshot', [
            'project' => $project->id,
        ]);

        // Assert
        $command->assertSuccessful();
        $command->expectsOutputToContain('Snapshot job dispatched for project');
    }
}
