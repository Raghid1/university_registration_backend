<?php

namespace App\Repositories;

use App\Models\Student;
use App\Repositories\Interfaces\StudentRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class StudentRepository extends BaseRepository implements StudentRepositoryInterface
{
    /**
     * StudentRepository constructor.
     *
     * @param Student $model
     */
    public function __construct(Student $model)
    {
        parent::__construct($model);
    }

    /**
     * Find a student by their email.
     *
     * @param string $email
     * @return Student|null
     */
    public function findByEmail(string $email): ?Student
    {
        return $this->model->where('email', $email)->first();
    }

    /**
     * Get all courses a student is registered for.
     *
     * @param int $studentId
     * @return Collection
     */
    public function getCourses(int $studentId): Collection
    {
        $cacheKey = "student_courses_{$studentId}";

        return Cache::remember($cacheKey,600,function() use ($studentId,$cacheKey){
            Log::info("Cache miss for key: {$cacheKey}");
            $student = $this->find($studentId);
            return $student ? $student->courses : new Collection();
        });
    }

    /**
     * Register a student for a course.
     *
     * @param int $studentId
     * @param int $courseId
     * @return bool
     */
    public function registerCourse(int $studentId, int $courseId): bool
    {
        $student = $this->find($studentId);
        if ($student && !$student->courses->contains($courseId)) {
            // `attach` method adds a record to the many-to-many pivot table
            $student->courses()->attach($courseId);

            // Clear cache to avoid stale data
            Cache::forget("student_courses_{$studentId}");
            return true;
        }
        return false;
    }

    /**
     * Unregister a student from a course.
     *
     * @param int $studentId
     * @param int $courseId
     * @return bool
     */
    public function unregisterCourse(int $studentId, int $courseId): bool
    {
        $student = $this->find($studentId);
        if ($student && $student->courses->contains($courseId)) {
            // `detach` method removes a record from the many-to-many pivot table
            $student->courses()->detach($courseId);
            // Clear cache to avoid stale data
            Cache::forget("student_courses_{$studentId}");
            return true;
        }
        return false;
    }

    public function studentscoursemath() :Collection{
        return $this->model->whereHas('courses',function($course){
            $course->where('name','math');
        })->get();
    }
}