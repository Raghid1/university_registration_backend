<?php

use App\Http\Controllers\Auth\StudentAuthController;
use App\Http\Controllers\CourseController;
use App\Http\Controllers\StudentController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Cache;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

// --- Public Routes (No Authentication Required) ---

// Student Registration & Authentication
Route::post('/register', [StudentAuthController::class, 'register']);
Route::post('/login', [StudentAuthController::class, 'login']);


// --- Protected Routes (Authentication Required via Sanctum) ---

Route::middleware('auth:sanctum')->group(function () {

    // Student Authentication
    Route::post('/logout', [StudentAuthController::class, 'logout']);
    Route::get('/me', [StudentAuthController::class, 'me']);

    // Student Management (CRUD)
    // Note: For 'store', you could use this route if you allow authenticated
    // users (e.g., admins) to create other student accounts.
    // Otherwise, rely solely on the public /register route.
    Route::apiResource('students', StudentController::class)->except(['store']); // Store is handled by /register for self-registration
    // Custom route to get courses for a student
    Route::get('student/courses', [StudentController::class, 'getCourses']);
    // Custom route for student registration for a course
    Route::post('students/{student}/register-course', [StudentController::class, 'registerForCourse']);
    // Custom route for student unregistration from a course
    Route::delete('students/{student}/unregister-course/{course}', [StudentController::class, 'unregisterFromCourse']);

    // Course Management (CRUD)
    Route::apiResource('courses', CourseController::class);
    // Custom route to get students for a course
    Route::get('courses/{course}/students', [CourseController::class, 'getStudents']);

    Route::get('courses/{course}/studentsnames', [CourseController::class, 'getStudentsNames']);


    Route::get('/cache-test', function () {
        Cache::put('test_key', 'hello redis', 600);
        return Cache::get('test_key');
    });
});