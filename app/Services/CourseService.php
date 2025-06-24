<?php

namespace App\Services;

use App\Models\Course;
use App\Repositories\Interfaces\CourseRepositoryInterface;
use App\Services\Interfaces\CourseServiceInterface;
use Illuminate\Database\Eloquent\Collection;

class CourseService implements CourseServiceInterface
{
    protected CourseRepositoryInterface $courseRepository;

    public function __construct(CourseRepositoryInterface $courseRepository)
    {
        $this->courseRepository = $courseRepository;
    }

    public function getAllCourses(): Collection
    {
        return $this->courseRepository->all();
    }

    public function getCourseById(int $id): ?Course
    {
        return $this->courseRepository->find($id);
    }

    public function createCourse(array $data): ?Course
    {
        return $this->courseRepository->create($data);
    }

    public function updateCourse(int $id, array $data): ?Course
    {
        $updated = $this->courseRepository->update($id, $data);
        return $updated ? $this->courseRepository->find($id) : null;
    }

    public function deleteCourse(int $id): bool
    {
        // When a course is deleted, the cascade delete on the course_student table
        // will automatically remove associated registrations.
        return $this->courseRepository->delete($id);
    }

    public function getCourseStudents(int $courseId): Collection
    {
        return $this->courseRepository->getStudents($courseId);
    }
}