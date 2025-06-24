<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Course extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'code',
        'description',
        'max_students',
        'professor_id',
    ];

    /**
     * Get the students registered for this course.
     */
    public function students()
    {
        // Define the many-to-many relationship
        return $this->belongsToMany(Student::class, 'course_student')
                    ->withTimestamps();
    }

    public function professor(){

        return $this->belongsTo(Professor::class,'professor_id');
    }
}