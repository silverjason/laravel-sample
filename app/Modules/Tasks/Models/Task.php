<?php

namespace App\Modules\Tasks\Models;

use App\Modules\Projects\Models\Project;
use App\Modules\Tasks\Database\Factories\TaskFactory;
use App\Modules\Tasks\Enums\TaskStatus;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * @property int $id
 * @property int $project_id
 * @property string $name
 * @property TaskStatus $status
 * @property ?Carbon $due_date
 * @property ?Carbon $completed_at
 * @property ?Carbon $deleted_at
 * @property Carbon $created_at
 * @property Carbon $updated_at
 * @property-read Project $project
 */
class Task extends Model
{
    use HasFactory, SoftDeletes;

    protected $guarded = ['id'];

    protected function casts(): array
    {
        return [
            'status' => TaskStatus::class,
            'due_date' => 'datetime',
            'completed_at' => 'datetime',
            'deleted_at' => 'datetime',
        ];
    }

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    protected static function newFactory(): TaskFactory
    {
        return TaskFactory::new();
    }
}
