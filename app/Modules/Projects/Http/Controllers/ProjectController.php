<?php

namespace App\Modules\Projects\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Projects\Actions\CompleteProject;
use App\Modules\Projects\Http\Requests\CompleteProjectRequest;
use App\Modules\Projects\Http\Requests\DestroyProjectRequest;
use App\Modules\Projects\Http\Requests\ListProjectsRequest;
use App\Modules\Projects\Http\Requests\ShowProjectRequest;
use App\Modules\Projects\Http\Requests\StoreProjectRequest;
use App\Modules\Projects\Http\Requests\UpdateProjectRequest;
use App\Modules\Projects\Http\Resources\ProjectResource;
use App\Modules\Projects\Models\Project;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Resources\Json\JsonResource;

class ProjectController extends Controller
{
    public function index(ListProjectsRequest $request): AnonymousResourceCollection
    {
        $projects = Project::query()
            ->withCount('tasks')
            ->when(
                $request->filled('search'),
                fn ($query) => $query->where('name', 'like', "%{$request->string('search')}%")
            )
            ->latest('id')
            ->paginate($request->integer('per_page', 15));

        return ProjectResource::collection($projects);
    }

    public function show(ShowProjectRequest $request, Project $project): JsonResource
    {
        return ProjectResource::make($project->loadCount('tasks'));
    }

    public function store(StoreProjectRequest $request): JsonResource
    {
        $project = $request->user()->projects()->create($request->validated());

        return ProjectResource::make($project->loadCount('tasks'));
    }

    public function update(UpdateProjectRequest $request, Project $project): JsonResource
    {
        $project->update($request->validated());

        return ProjectResource::make($project->loadCount('tasks'));
    }

    public function complete(CompleteProjectRequest $request, Project $project, CompleteProject $completeProject): JsonResource
    {
        return ProjectResource::make($completeProject->handle($project)->loadCount('tasks'));
    }

    public function destroy(DestroyProjectRequest $request, Project $project)
    {
        $project->delete();

        return response()->noContent();
    }
}
