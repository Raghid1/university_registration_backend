<?php

namespace App\Services;

use App\Models\Student;
use App\Repositories\Interfaces\CourseRepositoryInterface;
use App\Repositories\Interfaces\StudentRepositoryInterface;
use App\Services\Interfaces\StudentServiceInterface;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Hash; // For password hashing
use Illuminate\Support\Facades\Log; // For logging errors/information
use App\Events\CourseMaxCapacity;
use App\Events\StudentRegistration;

class StudentService implements StudentServiceInterface
{
    protected StudentRepositoryInterface $studentRepository;
    protected CourseRepositoryInterface $courseRepository;

    public function __construct(StudentRepositoryInterface $studentRepository, CourseRepositoryInterface $courseRepository)
    {
        $this->studentRepository = $studentRepository;
        $this->courseRepository = $courseRepository;
    }

    public function getAllStudents(): Collection
    {
        return $this->studentRepository->all();
    }

    public function getStudentById(int $id): ?Student
    {
        return $this->studentRepository->find($id);
    }

    public function createStudent(array $data): ?Student
    {
        // Hash the password before storing it
        $data['password'] = Hash::make($data['password']);
        $student = $this->studentRepository->create($data);
        event(new StudentRegistration($student));
        return $student;
    }

    public function updateStudent(int $id, array $data): ?Student
    {
        // Only hash password if it's provided in the update data
        if (isset($data['password'])) {
            $data['password'] = Hash::make($data['password']);
        }
        $updated = $this->studentRepository->update($id, $data);
        return $updated ? $this->studentRepository->find($id) : null;
    }

    public function deleteStudent(int $id): bool
    {
        return $this->studentRepository->delete($id);
    }

    public function getStudentCourses(int $studentId): Collection
    {
        return $this->studentRepository->getCourses($studentId);
    }

    /**
     * Register a student for a course, enforcing business rules.
     * Business Rules:
     * 1. Student can register for 3 courses max.
     * 2. Every course could have a max number of students of 3.
     *
     * @param int $studentId
     * @param int $courseId
     * @return array ['success' => bool, 'message' => string, 'student' => Student|null, 'course' => Course|null]
     */
    public function registerStudentForCourse(int $studentId, int $courseId): array
    {
        $student = $this->studentRepository->find($studentId);
        $course = $this->courseRepository->find($courseId);

        if (!$student) {
            return ['success' => false, 'message' => 'Student not found.', 'student' => null, 'course' => null];
        }
        if (!$course) {
            return ['success' => false, 'message' => 'Course not found.', 'student' => $student, 'course' => null];
        }

        // Rule 1: Student can register for 3 courses max
        if ($student->courses->count() >= 3) {
            return ['success' => false, 'message' => 'Student has reached the maximum course registration limit (3 courses).', 'student' => $student, 'course' => $course];
        }

        // Check if student is already registered for this course
        if ($student->courses->contains($courseId)) {
            return ['success' => false, 'message' => 'Student is already registered for this course.', 'student' => $student, 'course' => $course];
        }

        // Rule 2: Every course could have a max number of students of 3
        if ($course->students->count() >= $course->max_students) { // Using $course->max_students for flexibility
            return ['success' => false, 'message' => 'Course has reached its maximum student capacity.', 'student' => $student, 'course' => $course];
        }

        // If all checks pass, proceed with registration
        $registered = $this->studentRepository->registerCourse($studentId, $courseId);

        if($course->students->count()>=$course->max_students){
            CourseMaxCapacity::dispatch($course);
        }

        if ($registered) {
            // Refresh models to get the updated relationships count
            $student->load('courses'); // Eager load the updated courses relationship
            $course->load('students'); // Eager load the updated students relationship
            Log::info("Student ID: {$studentId} registered for Course ID: {$courseId}");
            return ['success' => true, 'message' => 'Student successfully registered for the course.', 'student' => $student, 'course' => $course];
        }

        Log::error("Failed to register student ID: {$studentId} for Course ID: {$courseId}");
        return ['success' => false, 'message' => 'Failed to register student for the course due to an unexpected error.', 'student' => $student, 'course' => $course];
    }

    public function unregisterStudentFromCourse(int $studentId, int $courseId): bool
    {
        $student = $this->studentRepository->find($studentId);
        $course = $this->courseRepository->find($courseId);

        if (!$student || !$course) {
            return false;
        }

        return $this->studentRepository->unregisterCourse($studentId, $courseId);
    }

    /**
     * Authenticate a student and return an API token.
     *
     * @param string $email
     * @param string $password
     * @return string|null The API token or null if authentication fails.
     */
    public function authenticateStudent(string $email, string $password): ?string
    {
        $student = $this->studentRepository->findByEmail($email);

        if (!$student || !Hash::check($password, $student->password)) {
            return null; // Authentication failed
        }

        // Delete existing tokens to ensure only one active token per student
        // This is a common practice for SPA/mobile API authentication
        $student->tokens()->delete();

        // Create a new API token for the student
        // 'auth_token' is the name of the token
        $token = $student->createToken('auth_token')->plainTextToken;

        return $token;
    }
}