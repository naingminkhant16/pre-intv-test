<?php

namespace App\Http\Requests;

use App\Enums\ProjectStatus;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ProjectRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return match ($this->method()) {
            'PUT', 'PATCH' => $this->updateRules(),
            default => $this->createRules(),
        };
    }

    private function updateRules(): array
    {
        return [
            'name' => [
                'required',
                'bail',
                'string',
                'max:100',
                'min:5',
                Rule::unique('projects', 'name')->ignore($this->route('project')),
            ],
            'description' => 'required|bail|string|max:255|min:5',
            'start_date' => 'required|date_format:Y-m-d',
            'end_date' => 'required|date_format:Y-m-d|after_or_equal:start_date',
            'status' => ['required', Rule::enum(ProjectStatus::class)],
        ];
    }

    private function createRules(): array
    {
        return [
            'name' => 'required|bail|string|max:100|min:5|unique:projects,name',
            'description' => 'required|bail|string|max:255|min:5',
            'start_date' => 'required|date_format:Y-m-d',
            'end_date' => 'required|date_format:Y-m-d|after_or_equal:start_date',
            'status' => ['required', Rule::enum(ProjectStatus::class)],
        ];
    }
}
