<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreStudentRequest;
use App\Services\Interfaces\StudentServiceInterface;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Tymon\JWTAuth\Facades\JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;

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
                // $token = $this->studentService->authenticateStudent($request->email, $request->password);
                $token = JWTAuth::fromUser($student);
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

        $credentials = $request->only('email', 'password');

        try {
            if (!$token = auth('student')->attempt($credentials)) {
                return response()->json(['error' => 'Invalid credentials'], 401);
            }

        } catch (\Exception $e) {
            return response()->json(['error' => 'Could not create token'], 500);
        }
        return response()->json([
            'message' => 'Logged in successfully!',
            'token' => $token
        ]);
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
            JWTAuth::invalidate(JWTAuth::getToken());

        } catch (JWTException $e) {
            return response()->json(['error' => 'Failed to logout, please try again'], 500);
        }
        return response()->json(['message' => 'Successfully logged out']);
    }

    /**
     * Get authenticated student details.
     *
     * @param Request $request
     * @return JsonResponse
     */
    // public function me(Request $request): JsonResponse
    // {
    //     // The 'auth:sanctum' middleware will ensure user is authenticated
    //     // and attach the user model to the request.
    //     return Response::json($request->user('sanctum'), 200);
    // }
}