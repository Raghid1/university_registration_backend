<?php

namespace App\Services\Interfaces;

use App\Models\Student;
use Illuminate\Database\Eloquent\Collection;

interface StudentServiceInterface
{
    public function getAllStudents(): Collection;
    public function getStudentById(int $id): ?Student;
    public function createStudent(array $data): ?Student;
    public function updateStudent(int $id, array $data): ?Student;
    public function deleteStudent(int $id): bool;
    public function getStudentCourses(int $studentId): Collection;
    public function registerStudentForCourse(int $studentId, int $courseId): array; // Return status and message
    public function unregisterStudentFromCourse(int $studentId, int $courseId): bool;
    public function authenticateStudent(string $email, string $password): ?string; // Returns token or null
}