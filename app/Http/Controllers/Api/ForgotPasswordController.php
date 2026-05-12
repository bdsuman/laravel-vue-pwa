<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\ForgotPasswordRequest;
use App\Http\Requests\ResetPasswordRequest;
use App\Http\Requests\VerifyOtpRequest;
use App\Services\PasswordResetService;
use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

final class ForgotPasswordController extends Controller
{
    public function __construct(
        private readonly PasswordResetService $passwordResetService
    ) {}

    public function sendOtp(ForgotPasswordRequest $request): JsonResponse
    {
        $result = $this->passwordResetService->sendOtp(
            email: $request->email,
            ipAddress: $request->ip()
        );

        return response()->json($result);
    }

    public function verifyOtp(VerifyOtpRequest $request): JsonResponse
    {
        $result = $this->passwordResetService->verifyOtp(
            email: $request->email,
            otp: $request->otp
        );

        if (! $result['success']) {
            return response()->json($result, Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        return response()->json($result);
    }

    public function resetPassword(ResetPasswordRequest $request): JsonResponse
    {
        $result = $this->passwordResetService->resetPassword(
            email: $request->email,
            token: $request->token,
            password: $request->password
        );

        if (! $result['success']) {
            return response()->json($result, Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        return response()->json($result);
    }
}
