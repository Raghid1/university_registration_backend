<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateCourseRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        // Only authenticated students (acting as 'admins' for now) can update courses.
        return auth('sanctum')->check();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $courseId = $this->route('course'); // Get the course ID from the route parameter

        return [
            'name' => 'sometimes|string|max:255',
            'code' => [
                'sometimes',
                'string',
                'max:20',
                Rule::unique('courses', 'code')->ignore($courseId), // Ignore current course's code
            ],
            'description' => 'nullable|string',
            'max_students' => 'nullable|integer|min:1|max:100',
        ];
    }
}