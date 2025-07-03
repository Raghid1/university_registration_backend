<?php

namespace App\Http\Controllers;

use App\Http\Requests\CourseRegistrationRequest;
use App\Http\Requests\StoreStudentRequest;
use App\Http\Requests\UpdateStudentRequest;
use App\Services\Interfaces\StudentServiceInterface;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Response;

class StudentController extends Controller
{
    protected StudentServiceInterface $studentService;

    public function __construct(StudentServiceInterface $studentService)
    {
        $this->studentService = $studentService;
    }

    /**
     * Display a listing of the students.
     *
     * @return JsonResponse
     */
    public function index(): JsonResponse
    {
        try {
            $students = $this->studentService->getAllStudents();
            return Response::json($students, 200);
        } catch (\Exception $e) {
            Log::error("Error fetching all students: " . $e->getMessage());
            return Response::json(['message' => 'Could not retrieve students.'], 500);
        }
    }

    /**
     * Store a newly created student in storage.
     * This is also handled by StudentAuthController::register, but kept for completeness
     * if you want to allow admin creation of students.
     *
     * @param StoreStudentRequest $request
     * @return JsonResponse
     */
    public function store(StoreStudentRequest $request): JsonResponse
    {
        try {
            $student = $this->studentService->createStudent($request->validated());
            return Response::json([
                'message' => 'Student created successfully!',
                'student' => $student,
            ], 201);
        } catch (\Exception $e) {
            Log::error("Error creating student: " . $e->getMessage());
            return Response::json(['message' => 'Failed to create student.'], 500);
        }
    }

    /**
     * Display the specified student.
     *
     * @param int $id
     * @return JsonResponse
     */
    public function show(int $id): JsonResponse
    {
        try {
            $student = $this->studentService->getStudentById($id);
            if ($student) {
                return Response::json($student, 200);
            }
            return Response::json(['message' => 'Student not found.'], 404);
        } catch (\Exception $e) {
            Log::error("Error fetching student ID {$id}: " . $e->getMessage());
            return Response::json(['message' => 'Could not retrieve student.'], 500);
        }
    }

    /**
     * Update the specified student in storage.
     *
     * @param UpdateStudentRequest $request
     * @param int $id
     * @return JsonResponse
     */
    public function update(UpdateStudentRequest $request, int $id): JsonResponse
    {
        // Ensure the authenticated student can only update their own record
        if (auth('student')->id() !== $id) {
            return Response::json(['message' => 'Unauthorized to update this student record.'], 403); // 403 Forbidden
        }

        try {
            $student = $this->studentService->updateStudent($id, $request->validated());
            if ($student) {
                return Response::json(['message' => 'Student updated successfully!', 'student' => $student], 200);
            }
            return Response::json(['message' => 'Student not found or failed to update.'], 404);
        } catch (\Exception $e) {
            Log::error("Error updating student ID {$id}: " . $e->getMessage());
            return Response::json(['message' => 'Failed to update student.'], 500);
        }
    }

    /**
     * Remove the specified student from storage.
     *
     * @param int $id
     * @return JsonResponse
     */
    public function destroy(int $id): JsonResponse
    {
        // For simplicity, let's allow an authenticated student to delete any student for now.
        // In a real app, this would be restricted to admin roles.
        // Or if you only allow a student to delete themselves:
        // if (auth('sanctum')->id() !== $id) {
        //     return Response::json(['message' => 'Unauthorized to delete this student record.'], 403);
        // }
        
        try {
            $deleted = $this->studentService->deleteStudent($id);
            if ($deleted) {
                return Response::json(['message' => 'Student deleted successfully!'], 200);
            }
            return Response::json(['message' => 'Student not found or failed to delete.'], 404);
        } catch (\Exception $e) {
            Log::error("Error deleting student ID {$id}: " . $e->getMessage());
            return Response::json(['message' => 'Failed to delete student.'], 500);
        }
    }

    /**
     * Get courses for a specific student.
     *
     * @param int $studentId
     * @return JsonResponse
     */
    public function getCourses(): JsonResponse
    {
        try {
            $student = auth('student')->user(); // Automatically get authenticated student

            if (!$student) {
                return Response::json(['message' => 'Unauthorized'], 401);
            }

            $courses = $this->studentService->getStudentCourses($student->id);
            $transformed= $courses->map(function($course){
                return[
                        "id"=> $course->id,
                        "name"=> $course->name,
                        "code"=> $course->code,
                        "description"=> $course->description,
                        "max_students"=> $course->max_students,
                        "professor_id"=> $course->professor_id,
                ];
            });
            return Response::json($transformed, 200);

        } catch (\Exception $e) {
            Log::error("Error fetching courses for student ID: " . $e->getMessage());
            return Response::json(['message' => 'Could not retrieve courses for student.'], 500);
        }
    }

    /**
     * Register an authenticated student for a course.
     *
     * @param CourseRegistrationRequest $request
     * @param int $studentId - This will be the ID of the authenticated student.
     * @return JsonResponse
     */
    public function registerForCourse(CourseRegistrationRequest $request, int $studentId): JsonResponse
    {
        // Ensure the authenticated student is the one trying to register
        if (auth('student')->id() !== $studentId) {
            return Response::json(['message' => 'Unauthorized to register for another student.'], 403);
        }

        try {
            $courseId = $request->validated('course_id');
            $result = $this->studentService->registerStudentForCourse($studentId, $courseId);

            if ($result['success']) {
                return Response::json([
                    'message' => $result['message'],
                    'student' => $result['student'], // Return updated student with courses
                    'course' => $result['course'], // Return updated course with students
                ], 200);
            } else {
                // Return appropriate HTTP status codes based on the business rule violation
                if (str_contains($result['message'], 'maximum course registration limit') || str_contains($result['message'], 'already registered')) {
                    return Response::json(['message' => $result['message']], 409); // 409 Conflict
                } elseif (str_contains($result['message'], 'maximum student capacity')) {
                    return Response::json(['message' => $result['message']], 409); // 409 Conflict
                } elseif (str_contains($result['message'], 'Student not found')) {
                    return Response::json(['message' => $result['message']], 404); // 404 Not Found
                } elseif (str_contains($result['message'], 'Course not found')) {
                    return Response::json(['message' => $result['message']], 404); // 404 Not Found
                }
                return Response::json(['message' => $result['message']], 500); // Generic server error
            }
        } catch (\Exception $e) {
            Log::error("Error registering student ID {$studentId} for course ID {$request->course_id}: " . $e->getMessage());
            return Response::json(['message' => 'An error occurred during course registration.'], 500);
        }
    }

    /**
     * Unregister an authenticated student from a course.
     *
     * @param Request $request
     * @param int $studentId - This will be the ID of the authenticated student.
     * @param int $courseId
     * @return JsonResponse
     */
    public function unregisterFromCourse(Request $request, int $studentId, int $courseId): JsonResponse
    {
        // Ensure the authenticated student is the one trying to unregister
        if (auth('student')->id() !== $studentId) {
            return Response::json(['message' => 'Unauthorized to unregister for another student.'], 403);
        }

        try {
            $unregistered = $this->studentService->unregisterStudentFromCourse($studentId, $courseId);

            if ($unregistered) {
                return Response::json(['message' => 'Successfully unregistered from course.'], 200);
            }
            return Response::json(['message' => 'Failed to unregister from course (student or course not found, or not registered).'], 400); // Bad request or not found/registered
        } catch (\Exception $e) {
            Log::error("Error unregistering student ID {$studentId} from course ID {$courseId}: " . $e->getMessage());
            return Response::json(['message' => 'An error occurred during course unregistration.'], 500);
        }
    }
}