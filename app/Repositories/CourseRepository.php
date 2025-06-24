<?php

namespace App\Repositories;

use App\Models\Course;
use App\Repositories\Interfaces\CourseRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;

class CourseRepository extends BaseRepository implements CourseRepositoryInterface
{
    /**
     * CourseRepository constructor.
     *
     * @param Course $model
     */
    public function __construct(Course $model)
    {
        parent::__construct($model);
    }

    /**
     * Get all students registered for a course.
     *
     * @param int $courseId
     * @return Collection
     */
    public function getStudents(int $courseId): Collection
    {
        $course = $this->find($courseId);
        return $course ? $course->students : new Collection();
    }

    /**
     * Find a course by its code.
     *
     * @param string $code
     * @return Course|null
     */
    public function findByCode(string $code): ?Course
    {
        return $this->model->where('code', $code)->first();
    }
}