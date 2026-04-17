<?php

namespace App\Modules\Projects\Policies;

use App\Modules\Projects\Models\Project;
use Illuminate\Contracts\Auth\Authenticatable;

class ProjectPolicy
{
    public function viewAny(Authenticatable $user): bool
    {
        return true;
    }

    public function view(Authenticatable $user, Project $project): bool
    {
        return true;
    }

    public function create(Authenticatable $user): bool
    {
        return true;
    }

    public function update(Authenticatable $user, Project $project): bool
    {
        return true;
    }

    public function delete(Authenticatable $user, Project $project): bool
    {
        return true;
    }

    public function complete(Authenticatable $user, Project $project): bool
    {
        return true;
    }
}
