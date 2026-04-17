<?php

namespace App\Modules\Tasks\Policies;

use App\Modules\Tasks\Models\Task;
use Illuminate\Contracts\Auth\Authenticatable;

class TaskPolicy
{
    public function viewAny(Authenticatable $user): bool
    {
        return true;
    }

    public function view(Authenticatable $user, Task $task): bool
    {
        return true;
    }

    public function create(Authenticatable $user): bool
    {
        return true;
    }

    public function update(Authenticatable $user, Task $task): bool
    {
        return true;
    }

    public function delete(Authenticatable $user, Task $task): bool
    {
        return true;
    }

    public function complete(Authenticatable $user, Task $task): bool
    {
        return true;
    }
}
