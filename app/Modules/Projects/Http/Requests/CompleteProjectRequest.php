<?php

namespace App\Modules\Projects\Http\Requests;

use App\Modules\Projects\Models\Project;
use Illuminate\Foundation\Http\FormRequest;

/**
 * @property-read Project $project
 */
class CompleteProjectRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('complete', $this->project);
    }

    public function rules(): array
    {
        return [];
    }
}
