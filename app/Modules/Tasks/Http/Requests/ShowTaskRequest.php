<?php

namespace App\Modules\Tasks\Http\Requests;

use App\Modules\Tasks\Models\Task;
use Illuminate\Foundation\Http\FormRequest;

/**
 * @property-read Task $task
 */
class ShowTaskRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('view', $this->task);
    }

    public function rules(): array
    {
        return [];
    }
}
