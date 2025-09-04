<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class RecruitmentRequest extends FormRequest
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
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'position'   => 'required|string|max:255',
            'department' => 'required|string|max:255',
            'quantity'   => 'required|integer|min:1',
            'job_type'   => 'required|in:full-time,part-time,internship',
            'start_date' => 'required|date',
            'deadline'   => 'required|date|after_or_equal:start_date',
            'status'     => 'required|in:open,closed,pending',
            'notes'      => 'nullable|string',
        ];
    }
}
