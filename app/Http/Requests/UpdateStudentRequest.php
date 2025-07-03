<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateStudentRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    // public function authorize(): bool
    // {
    //     // Only the authenticated student can update their own profile,
    //     // or an admin user (if you implement roles later).
    //     // For now, let's allow the authenticated user to update their own profile.
    //     // We'll handle checking if the authenticated user ID matches the student ID in the controller.
    //     return auth('sanctum')->check();
    // }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $studentId = $this->route('student'); // Get the student ID from the route parameter

        return [
            'name' => 'sometimes|string|max:255',
            'email' => [
                'sometimes',
                'string',
                'email',
                'max:255',
                Rule::unique('students', 'email')->ignore($studentId), // Ignore current student's email
            ],
            'password' => 'sometimes|string|min:8|confirmed',
        ];
    }
}