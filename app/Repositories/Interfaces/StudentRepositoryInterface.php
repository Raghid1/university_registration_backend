<?php

namespace App\Repositories\Interfaces;

use App\Models\Student;
use Illuminate\Database\Eloquent\Collection;

interface StudentRepositoryInterface extends BaseRepositoryInterface
{
    public function findByEmail(string $email): ?Student;
    public function getCourses(int $studentId): Collection;
    public function registerCourse(int $studentId, int $courseId): bool;
    public function unregisterCourse(int $studentId, int $courseId): bool;
}