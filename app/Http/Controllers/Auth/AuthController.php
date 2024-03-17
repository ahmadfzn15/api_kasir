<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Auth\Events\Registered;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use Validator;

class AuthController extends Controller
{
    public function index(Request $request) {
        try {
            $user = $request->user();

            $response = [
                "success" => true,
                "data" => $user,
                "message" => "Data User"
            ];

            return response()->json($response, 200);
        } catch (\Throwable $th) {
            $response = [
                "success" => false,
                "data" => $user,
                "message" => "Gagal mengambil data user"
            ];

            return response()->json($response, 500);
        }
    }

    public function login(Request $request)
    {
        try {
            $validated = Validator::make($request->all(), [
                'username' => 'required|string',
                'password' => 'required|string'
            ]);

            if ($validated->fails()) {
                $response = [
                    'success' => false,
                    'message' => "Username dan password wajib diisi!"
                ];

                return response()->json($response, 500);
            }

            if (!Auth::attempt($request->all())) {
                $response = [
                    'success' => false,
                    'message' => "Username atau password yang anda masukkan salah."
                ];

                return response()->json($response, 500);
            }

            $user = User::where('username', $request->username)->first();
            $token = $user->createToken('authToken')->plainTextToken;

            $response = [
                "success" => true,
                "data" => [
                    "token" => $token,
                    "role" => $user->role,
                ],
                "message" => "Login Berhasil"
            ];

            return response()->json($response, 200);
        } catch (\Throwable $th) {
            $response = [
                'success' => false,
                'message' => "Login gagal"
            ];

            return response()->json($response, 500);
        }
    }

    public function register(Request $request)
    {
        try {
            $validated = Validator::make($request->all(), [
                'username' => ['required', 'string', 'max:255', 'unique:users'],
                'email' => ['nullable', 'email', 'unique:users'],
                'password' => ['required'],
            ]);

            if ($validated->fails()) {
                $response = [
                    'success' => false,
                    'message' => $validated->errors
                ];

                return response()->json($response, 500);
            }

            $user = User::create([
                'username' => $request->username,
                'password' => Hash::make($request->password),
                'role' => 'admin',
            ]);

            event(new Registered($user));

            Auth::login($user);

            return response()->json("Daftar berhasil.", 200);
        } catch (\Throwable $th) {
            $response = [
                'success' => false,
                'message' => "Daftar gagal"
            ];

            return response()->json($response, 500);
        }
    }

    public function update(Request $request)
    {
        $user = $request->user();
        try {
            $validated = Validator::make($request->all(), [
                'foto' => 'nullable|image|mimes:png,jpg,jpeg',
                'username' => 'required|string|max:255',
                'email' => 'nullable|email',
            ]);

            // if ($validated->fails()) {
            //     $response = [
            //         'success' => false,
            //         'message' => $validated->errors
            //     ];

            //     return response()->json($response, 500);
            // }

            $imageName = null;
            if ($request->hasFile('foto')) {
                if ($user->foto) {
                    Storage::delete("img/" . $user->foto);
                }
                $image = $request->file('foto');
                $imageName = time().'.'.$image->getClientOriginalExtension();
                $image->storeAs('public/img', $imageName);
            }

            User::findOrFail($user->id)->update([
                'foto' => $imageName,
                'username' => $request->username,
                'email' => $request->email,
                'no_tlp' => $request->no_tlp,
            ]);

            $response = [
                "success" => true,
                "message" => "Profil berhasil diubah."
            ];

            return response()->json($response, 200);
        } catch (\Throwable $th) {
            $response = [
                "success" => false,
                "message" => "Profil gagal diubah"
            ];

            return response()->json($response, 500);
        }
    }

    public function destroy(int $id, Request $request)
    {
        $res = Auth::attempt(["email" => $user->email, "password" => $request->password]);

        if ($res) {
            $user->delete();

            $response = [
                'success' => true,
                'message' => "Akun berhasil dihapus"
            ];

            return response()->json($response, 200);
        } else {
            $response = [
                'success' => false,
                'message' => "Akun gagal dihapus"
            ];

            return response()->json($response, 500);
        }
    }

    public function logout(Request $request) {
        $request->user()->currentAccessToken()->delete();

        $response = [
            'success' => true,
            'message' => "Logout berhasil"
        ];

        return response()->json($response, 200);
    }
}
