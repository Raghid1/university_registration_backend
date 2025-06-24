<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreStudentRequest;
use App\Services\Interfaces\StudentServiceInterface;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Response;

class StudentAuthController extends Controller
{
    protected StudentServiceInterface $studentService;

    public function __construct(StudentServiceInterface $studentService)
    {
        $this->studentService = $studentService;
    }

    /**
     * Register a new student.
     *
     * @param StoreStudentRequest $request
     * @return JsonResponse
     */
    public function register(StoreStudentRequest $request): JsonResponse
    {
        try {
            $student = $this->studentService->createStudent($request->validated());

            if ($student) {
                // Optionally log the student in immediately after registration
                $token = $this->studentService->authenticateStudent($request->email, $request->password);
                return Response::json([
                    'message' => 'Student registered successfully!',
                    'student' => $student,
                    'token' => $token,
                ], 201); // 201 Created
            }

            return Response::json(['message' => 'Student registration failed.'], 500);

        } catch (\Exception $e) {
            Log::error("Student registration error: " . $e->getMessage());
            return Response::json(['message' => 'An error occurred during registration.'], 500);
        }
    }

    /**
     * Authenticate a student and issue an API token.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function login(Request $request): JsonResponse
    {
        $request->validate([
            'email' => 'required|string|email',
            'password' => 'required|string',
        ]);

        try {
            $token = $this->studentService->authenticateStudent($request->email, $request->password);

            if ($token) {
                return Response::json(['message' => 'Logged in successfully!', 'token' => $token], 200);
            }

            return Response::json(['message' => 'Invalid credentials.'], 401); // 401 Unauthorized

        } catch (\Exception $e) {
            Log::error("Student login error: " . $e->getMessage());
            return Response::json(['message' => 'An error occurred during login.'], 500);
        }
    }

    /**
     * Log out the authenticated student (revoke current token).
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function logout(Request $request): JsonResponse
    {
        try {
            /** @var \App\Models\Student|null $student */
            $student = $request->user('sanctum'); // Get the authenticated student via Sanctum

            if ($student) {
                // Revoke the current token.
                // The 'token()' method on the authenticated user returns the PersonalAccessToken instance
                // that was used to authenticate the current request.
                // This is a robust alternative to currentAccessToken()->delete().
                $student->token()->delete();
                return Response::json(['message' => 'Logged out successfully.'], 200);
            }

            return Response::json(['message' => 'No authenticated user to log out.'], 401); // 401 Unauthorized

        } catch (\Exception $e) {
            Log::error("Student logout error: " . $e->getMessage());
            return Response::json(['message' => 'An error occurred during logout.'], 500);
        }
    }

    /**
     * Get authenticated student details.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function me(Request $request): JsonResponse
    {
        // The 'auth:sanctum' middleware will ensure user is authenticated
        // and attach the user model to the request.
        return Response::json($request->user('sanctum'), 200);
    }
}