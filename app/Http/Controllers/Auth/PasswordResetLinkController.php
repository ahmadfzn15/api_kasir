<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;
use Illuminate\Validation\ValidationException;
use Validator;

class PasswordResetLinkController extends Controller
{
    /**
     * Handle an incoming password reset link request.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request): JsonResponse
    {
        Validator::make($request->only('email'), [
            'email' => ['required', 'email'],
        ]);

        $status = Password::sendResetLink(
            ["email" => $request->email]
        );

        if ($status != Password::RESET_LINK_SENT) {
            return response()->json(['message' => $status], 500);
        }

        return response()->json(['message' => "Email ubah kata sandi telah dikirim ke email " . $request->email], 200);
    }
}
