<?php

namespace App\Modules\Tasks\Actions;

use App\Modules\Tasks\Enums\TaskStatus;
use App\Modules\Tasks\Models\Task;
use Illuminate\Validation\ValidationException;

class CompleteTask
{
    public function handle(Task $task): Task
    {
        if ($task->status === TaskStatus::Completed) {
            return $task;
        }

        if ($task->status === TaskStatus::Blocked) {
            throw ValidationException::withMessages([
                'status' => 'Blocked tasks cannot be completed.',
            ]);
        }

        $task->update([
            'status' => TaskStatus::Completed,
            'completed_at' => now(),
        ]);

        return $task->fresh();
    }
}
