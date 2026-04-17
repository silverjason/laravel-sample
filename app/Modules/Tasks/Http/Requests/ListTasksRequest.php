<?php

namespace App\Modules\Tasks\Http\Requests;

use App\Modules\Tasks\Models\Task;
use Illuminate\Foundation\Http\FormRequest;

class ListTasksRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('viewAny', Task::class);
    }

    public function rules(): array
    {
        return [
            'search' => ['nullable', 'string', 'max:255'],
            'per_page' => ['nullable', 'integer', 'min:1', 'max:100'],
        ];
    }
}
