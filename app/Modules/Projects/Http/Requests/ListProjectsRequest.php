<?php

namespace App\Modules\Projects\Http\Requests;

use App\Modules\Projects\Models\Project;
use Illuminate\Foundation\Http\FormRequest;

class ListProjectsRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('viewAny', Project::class);
    }

    public function rules(): array
    {
        return [
            'search' => ['nullable', 'string', 'max:255'],
            'per_page' => ['nullable', 'integer', 'min:1', 'max:100'],
        ];
    }
}
