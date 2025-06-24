<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreCourseRequest;
use App\Http\Requests\UpdateCourseRequest;
use App\Services\Interfaces\CourseServiceInterface;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Response;

class CourseController extends Controller
{
    protected CourseServiceInterface $courseService;

    public function __construct(CourseServiceInterface $courseService)
    {
        $this->courseService = $courseService;
    }

    /**
     * Display a listing of the courses.
     *
     * @return JsonResponse
     */
    public function index(): JsonResponse
    {
        try {
            $courses = $this->courseService->getAllCourses();
            return Response::json($courses, 200);
        } catch (\Exception $e) {
            Log::error("Error fetching all courses: " . $e->getMessage());
            return Response::json(['message' => 'Could not retrieve courses.'], 500);
        }
    }

    /**
     * Store a newly created course in storage.
     *
     * @param StoreCourseRequest $request
     * @return JsonResponse
     */
    public function store(StoreCourseRequest $request): JsonResponse
    {
        try {
            $course = $this->courseService->createCourse($request->validated());
            return Response::json([
                'message' => 'Course created successfully!',
                'course' => $course,
            ], 201);
        } catch (\Exception $e) {
            Log::error("Error creating course: " . $e->getMessage());
            return Response::json(['message' => 'Failed to create course.'], 500);
        }
    }

    /**
     * Display the specified course.
     *
     * @param int $id
     * @return JsonResponse
     */
    public function show(int $id): JsonResponse
    {
        try {
            $course = $this->courseService->getCourseById($id);
            if ($course) {
                return Response::json($course, 200);
            }
            return Response::json(['message' => 'Course not found.'], 404);
        } catch (\Exception $e) {
            Log::error("Error fetching course ID {$id}: " . $e->getMessage());
            return Response::json(['message' => 'Could not retrieve course.'], 500);
        }
    }

    /**
     * Update the specified course in storage.
     *
     * @param UpdateCourseRequest $request
     * @param int $id
     * @return JsonResponse
     */
    public function update(UpdateCourseRequest $request, int $id): JsonResponse
    {
        try {
            $course = $this->courseService->updateCourse($id, $request->validated());
            if ($course) {
                return Response::json(['message' => 'Course updated successfully!', 'course' => $course], 200);
            }
            return Response::json(['message' => 'Course not found or failed to update.'], 404);
        } catch (\Exception $e) {
            Log::error("Error updating course ID {$id}: " . $e->getMessage());
            return Response::json(['message' => 'Failed to update course.'], 500);
        }
    }

    /**
     * Remove the specified course from storage.
     *
     * @param int $id
     * @return JsonResponse
     */
    public function destroy(int $id): JsonResponse
    {
        try {
            $deleted = $this->courseService->deleteCourse($id);
            if ($deleted) {
                return Response::json(['message' => 'Course deleted successfully!'], 200);
            }
            return Response::json(['message' => 'Course not found or failed to delete.'], 404);
        } catch (\Exception $e) {
            Log::error("Error deleting course ID {$id}: " . $e->getMessage());
            return Response::json(['message' => 'Failed to delete course.'], 500);
        }
    }

    /**
     * Get students registered for a specific course.
     *
     * @param int $courseId
     * @return JsonResponse
     */
    public function getStudents(int $courseId): JsonResponse
    {
        try {
            $course = $this->courseService->getCourseById($courseId);
            if (!$course) {
                return Response::json(['message' => 'Course not found.'], 404);
            }
            $students = $this->courseService->getCourseStudents($courseId);
            return Response::json($students, 200);
        } catch (\Exception $e) {
            Log::error("Error fetching students for course ID {$courseId}: " . $e->getMessage());
            return Response::json(['message' => 'Could not retrieve students for course.'], 500);
        }
    }
    public function getStudentsNames(int $courseId): JsonResponse
    {
        try {
            $course = $this->courseService->getCourseById($courseId);
            if (!$course) {
                return Response::json(['message' => 'Course not found.'], 404);
            }
            $students = $this->courseService->getCourseStudents($courseId);
            $names= $students->pluck('name');
            return Response::json($names, 200);
        } catch (\Exception $e) {
            Log::error("Error fetching students for course ID {$courseId}: " . $e->getMessage());
            return Response::json(['message' => 'Could not retrieve students for course.'], 500);
        }
    }
}
