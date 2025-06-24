<?php

namespace App\Services\Interfaces;

use App\Models\Course;
use Illuminate\Database\Eloquent\Collection;

interface CourseServiceInterface
{
    public function getAllCourses(): Collection;
    public function getCourseById(int $id): ?Course;
    public function createCourse(array $data): ?Course;
    public function updateCourse(int $id, array $data): ?Course;
    public function deleteCourse(int $id): bool;
    public function getCourseStudents(int $courseId): Collection;
}