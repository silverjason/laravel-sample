<?php

namespace App\Modules\Tasks\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Tasks\Actions\CompleteTask;
use App\Modules\Tasks\Http\Requests\CompleteTaskRequest;
use App\Modules\Tasks\Http\Requests\DestroyTaskRequest;
use App\Modules\Tasks\Http\Requests\ListTasksRequest;
use App\Modules\Tasks\Http\Requests\ShowTaskRequest;
use App\Modules\Tasks\Http\Requests\StoreTaskRequest;
use App\Modules\Tasks\Http\Requests\UpdateTaskRequest;
use App\Modules\Tasks\Http\Resources\TaskResource;
use App\Modules\Tasks\Models\Task;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Resources\Json\JsonResource;

class TaskController extends Controller
{
    public function index(ListTasksRequest $request): AnonymousResourceCollection
    {
        $tasks = Task::query()
            ->with('project')
            ->when(
                $request->filled('search'),
                fn ($query) => $query->where('name', 'like', "%{$request->string('search')}%")
            )
            ->latest('id')
            ->paginate($request->integer('per_page', 15));

        return TaskResource::collection($tasks);
    }

    public function show(ShowTaskRequest $request, Task $task): JsonResource
    {
        return TaskResource::make($task->load('project'));
    }

    public function store(StoreTaskRequest $request): JsonResource
    {
        $task = Task::query()->create($request->validated());

        return TaskResource::make($task->load('project'));
    }

    public function update(UpdateTaskRequest $request, Task $task): JsonResource
    {
        $task->update($request->validated());

        return TaskResource::make($task->load('project'));
    }

    public function complete(CompleteTaskRequest $request, Task $task, CompleteTask $completeTask): JsonResource
    {
        return TaskResource::make($completeTask->handle($task)->load('project'));
    }

    public function destroy(DestroyTaskRequest $request, Task $task)
    {
        $task->delete();

        return response()->noContent();
    }
}
