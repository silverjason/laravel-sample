<?php

namespace App\Modules\Projects\Http\Requests;

use App\Modules\Projects\Models\Project;
use Illuminate\Foundation\Http\FormRequest;

/**
 * @property-read Project $project
 */
class DestroyProjectRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('delete', $this->project);
    }

    public function rules(): array
    {
        return [];
    }
}
