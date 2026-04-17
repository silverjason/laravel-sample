<?php

namespace App\Modules\Projects\Actions;

use App\Modules\Projects\Enums\ProjectStatus;
use App\Modules\Projects\Models\Project;
use Illuminate\Validation\ValidationException;

class CompleteProject
{
    public function handle(Project $project): Project
    {
        if ($project->status === ProjectStatus::Archived) {
            throw ValidationException::withMessages([
                'status' => 'Archived projects cannot be completed.',
            ]);
        }

        if ($project->status === ProjectStatus::Completed) {
            return $project;
        }

        $project->update([
            'status' => ProjectStatus::Completed,
            'started_at' => $project->started_at ?? now(),
            'completed_at' => now(),
        ]);

        return $project->fresh();
    }
}
