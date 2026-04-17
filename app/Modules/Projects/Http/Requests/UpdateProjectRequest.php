<?php

namespace App\Modules\Projects\Http\Requests;

use App\Modules\Projects\Enums\ProjectPriority;
use App\Modules\Projects\Enums\ProjectStatus;
use App\Modules\Projects\Models\Project;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Unique;
use Illuminate\Validation\Rule;

/**
 * @property-read Project $project
 */
class UpdateProjectRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('update', $this->project);
    }

    public function rules(): array
    {
        return [
            'name' => [
                'sometimes',
                'string',
                'max:255',
                $this->uniqueNameRule(),
            ],
            'description' => ['sometimes', 'nullable', 'string'],
            'status' => ['sometimes', Rule::enum(ProjectStatus::class)],
            'priority' => ['sometimes', Rule::enum(ProjectPriority::class)],
            'due_date' => ['sometimes', 'nullable', 'date'],
            'started_at' => ['sometimes', 'nullable', 'date'],
            'completed_at' => ['sometimes', 'nullable', 'date', 'after_or_equal:started_at'],
        ];
    }

    private function uniqueNameRule(): Unique
    {
        return Rule::unique('projects', 'name')
            ->where('user_id', $this->user()->getAuthIdentifier())
            ->ignore($this->project);
    }
}
