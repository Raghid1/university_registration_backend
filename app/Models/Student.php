<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable; // Important for Sanctum
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens; // Important for Sanctum

class Student extends Authenticatable // Extend Authenticatable
{
    use HasFactory, Notifiable, HasApiTokens; // Use HasApiTokens

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
    ];

    /**
     * Get the courses that the student is registered for.
     */
    public function courses()
    {
        // Define the many-to-many relationship
        // with `pivot` to access additional columns on the intermediate table if any,
        // although for now we don't have any specific columns on the pivot.
        return $this->belongsToMany(Course::class, 'course_student')
                    ->withTimestamps(); // Adds created_at/updated_at to pivot table
    }
}
