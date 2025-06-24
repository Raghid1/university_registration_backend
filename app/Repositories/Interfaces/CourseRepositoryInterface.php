<?php

namespace App\Repositories\Interfaces;

use Illuminate\Database\Eloquent\Collection;

interface CourseRepositoryInterface extends BaseRepositoryInterface
{
    public function getStudents(int $courseId): Collection;//get students in a course
    public function findByCode(string $code): ?\App\Models\Course;
}