<?php

namespace App\Modules\Projects\Http\Requests;

use App\Modules\Projects\Models\Project;
use Illuminate\Foundation\Http\FormRequest;

/**
 * @property-read Project $project
 */
class ShowProjectRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('view', $this->project);
    }

    public function rules(): array
    {
        return [];
    }
}
