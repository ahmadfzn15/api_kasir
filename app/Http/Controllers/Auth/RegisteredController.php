<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;

class RegisteredController extends Controller
{
    public function store(Request $request): Response
    {
        try {
            $request->validate([
                'username' => ['required', 'string', 'max:255'],
                'password' => ['required', 'confirmed', Rules\Password::defaults()],
            ]);

            $user = User::create([
                'username' => $request->username,
                'password' => Hash::make($request->password),
                'role' => 'admin',
            ]);

            event(new Registered($user));

            Auth::login($user);

            $response = [
                "success" => true,
                "message" => "Daftar berhasil"
            ];

            return response()->json($response, 200);
        } catch (\Throwable $th) {
            $response = [
                "success" => false,
                "message" => "Daftar gagal"
            ];

            return response()->json($response, 500);
        }
    }
}
