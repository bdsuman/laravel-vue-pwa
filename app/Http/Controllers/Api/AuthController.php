<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\LoginRequest;
use App\Http\Requests\StoreUserRequest;
use App\Http\Resources\UserResource;
use App\Models\User;
use App\Services\UserService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Symfony\Component\HttpFoundation\Response;

final class AuthController extends Controller
{
    public function __construct(
        private readonly UserService $userService
    ) {}

    public function register(StoreUserRequest $request): JsonResponse
    {
        $user = $this->userService->create(
            \App\DataTransferObjects\UserDTO::fromArray($request->validated())
        );

        $token = Auth::guard('sanctum')->login($user);

        return $this->successResponse(
            [
                'user' => new UserResource($user),
                'token' => $token,
                'token_type' => 'Bearer',
            ],
            'Registration successful',
            Response::HTTP_CREATED
        );
    }

    public function login(LoginRequest $request): JsonResponse
    {
        $credentials = $request->only(['email', 'password']);

        if (! Auth::guard('sanctum')->attempt($credentials)) {
            return $this->errorResponse(
                'Invalid credentials',
                Response::HTTP_UNAUTHORIZED
            );
        }

        $user = Auth::guard('sanctum')->user();
        $token = Auth::guard('sanctum')->login($user);

        return $this->successResponse([
            'user' => new UserResource($user),
            'token' => $token,
            'token_type' => 'Bearer',
        ], 'Login successful');
    }

    public function logout(Request $request): JsonResponse
    {
        Auth::guard('sanctum')->logout();

        return $this->successResponse(null, 'Logged out successfully');
    }

    public function user(Request $request): JsonResponse
    {
        return $this->successResponse(new UserResource($request->user()));
    }

    public function refresh(Request $request): JsonResponse
    {
        $user = $request->user();
        $token = Auth::guard('sanctum')->login($user);

        return $this->successResponse([
            'token' => $token,
            'token_type' => 'Bearer',
        ], 'Token refreshed');
    }

    protected function successResponse(
        mixed $data = null,
        string $message = 'Success',
        int $status = Response::HTTP_OK
    ): JsonResponse {
        return response()->json([
            'success' => true,
            'message' => $message,
            'data' => $data,
        ], $status);
    }

    protected function errorResponse(
        string $message = 'Error',
        int $status = Response::HTTP_BAD_REQUEST,
        array $errors = []
    ): JsonResponse {
        return response()->json([
            'success' => false,
            'message' => $message,
            'errors' => $errors,
        ], $status);
    }
}
