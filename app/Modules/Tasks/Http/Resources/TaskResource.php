<?php

namespace App\Modules\Tasks\Http\Resources;

use App\Modules\Projects\Http\Resources\ProjectResource;
use App\Modules\Tasks\Models\Task;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin Task
 */
class TaskResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'project_id' => $this->project_id,
            'name' => $this->name,
            'status' => $this->status->value,
            'due_date' => $this->due_date?->toISOString(),
            'completed_at' => $this->completed_at?->toISOString(),
            'project' => ProjectResource::make($this->whenLoaded('project')),
            'created_at' => $this->created_at->toISOString(),
            'updated_at' => $this->updated_at->toISOString(),
            'deleted_at' => $this->deleted_at?->toISOString(),
        ];
    }
}
