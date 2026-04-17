<?php

namespace App\Modules\Projects\Console\Commands;

use App\Modules\Projects\Jobs\GenerateProjectSnapshotJob;
use App\Modules\Projects\Models\Project;
use Illuminate\Console\Command;

class DispatchProjectSnapshotCommand extends Command
{
    protected $signature = 'projects:snapshot {project : The project id}';

    protected $description = 'Dispatch a project snapshot job for the given project';

    public function handle(): int
    {
        $project = Project::query()->findOrFail($this->argument('project'));

        GenerateProjectSnapshotJob::dispatch($project->id)->onConnection('database');

        $this->components->info("Snapshot job dispatched for project [{$project->id}].");

        return self::SUCCESS;
    }
}
