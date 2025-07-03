<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreCourseRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    // public function authorize(): bool
    // {
    //     // For simplicity, let's assume any authenticated student can create a course for now.
    //     // In a real app, this would be restricted to admin/instructor roles.
    //     return auth('sanctum')->check();
    // }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:20|unique:courses,code',
            'description' => 'nullable|string',
            'max_students' => 'nullable|integer|min:1|max:100', // Allow setting max_students, default is 3
        ];
    }
}