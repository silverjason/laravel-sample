<?php

namespace App\Modules\Tasks\Http\Requests;

use App\Modules\Tasks\Enums\TaskStatus;
use App\Modules\Tasks\Models\Task;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Exists;

class StoreTaskRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('create', Task::class);
    }

    public function rules(): array
    {
        return [
            'project_id' => ['required', 'integer', $this->projectExistsRule()],
            'name' => ['required', 'string', 'max:255'],
            'status' => ['required', Rule::enum(TaskStatus::class)],
            'due_date' => ['nullable', 'date'],
            'completed_at' => ['nullable', 'date'],
        ];
    }

    private function projectExistsRule(): Exists
    {
        return Rule::exists('projects', 'id')
            ->where('user_id', $this->user()->getAuthIdentifier());
    }
}
