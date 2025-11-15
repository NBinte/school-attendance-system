<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreStudentRequest extends FormRequest
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
            'name'       => ['required', 'string', 'max:255'],
            'student_id' => ['required', 'string', 'max:50', 'unique:students,student_id'],
            'class'      => ['required', 'string', 'max:50'],
            'section'    => ['nullable', 'string', 'max:50'],
            // for now we'll treat photo as a URL/path string; we can switch to file upload later
            'photo'      => ['nullable', 'string', 'max:255'],
        ];
    }
}
