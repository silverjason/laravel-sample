<?php

namespace App\Modules\Projects\Jobs;

use App\Modules\Projects\Models\Project;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Log;

class GenerateProjectSnapshotJob implements ShouldQueue
{
    use Queueable;

    public function __construct(public int $projectId) {}

    public function handle(): void
    {
        $project = Project::query()->withCount('tasks')->find($this->projectId);

        if (! $project) {
            return;
        }

        Log::info('Generated project snapshot.', [
            'project_id' => $project->id,
            'status' => $project->status->value,
            'priority' => $project->priority->value,
            'tasks_count' => $project->tasks_count,
        ]);
    }
}
