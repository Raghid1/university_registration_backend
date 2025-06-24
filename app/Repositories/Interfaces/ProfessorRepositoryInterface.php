<?php

namespace App\Repositories\Interfaces;

use App\Models\Student;
use App\Models\Professor;
use Illuminate\Database\Eloquent\Collection;

interface ProfessorRepositoryInterface extends BaseRepositoryInterface
{
    public function findByEmail(string $email): ?Professor;
    public function getCourses(int $professorId): Collection;//get students in a course
}