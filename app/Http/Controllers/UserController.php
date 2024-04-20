<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Hash;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use App\Models\User;
use Validator;

class UserController extends Controller
{
    public function index(Request $request) {
        try {
            $data = $request->user();
            $user = User::with('market')->findOrFail($data->id);

            $response = [
                "success" => true,
                "data" => $user,
                "message" => "Data User"
            ];

            return response()->json($response, 200);
        } catch (\Throwable $th) {
            $response = [
                "success" => false,
                "message" => "Terjadi Kesalahan"
            ];

            return response()->json($response, 500);
        }
    }

    public function update(Request $request)
    {
        try {
            $validated = Validator::make($request->all(), [
                'old_img' => 'nullable|string', 
                'new_img' => 'nullable|image',
                'nama' => 'required|string|max:255',
                'email' => 'required|email',
            ]);

            if ($validated->fails()) {
                $response = [
                    'success' => false,
                    'message' => $validated->errors(),
                ];

                return response()->json($response, 400);
            }

            $user = $request->user();
            $new_img = null;
            if ($request->hasFile('new_img')) {
                if ($user->foto) {
                    Storage::delete("img/" . $user->foto);
                }
                $image = $request->file('new_img');
                $new_img = time().'.'.$image->getClientOriginalExtension();
                $image->storeAs('public/img', $new_img);
            } else if ($user->foto && !$request->has('old_img')) {
                Storage::delete("img/" . $user->foto);
            }

            $user->fill([
                'foto' => $new_img ?? $request->old_img,
                'nama' => $request->nama,
                'email' => $request->email,
                'no_tlp' => $request->no_tlp,
            ]);
            $request->user()->save();

            $response = [
                "success" => true,
                "message" => "Profil berhasil diubah."
            ];

            return response()->json($response, 200);
        } catch (\Throwable $th) {
            $response = [
                "success" => false,
                "message" => "Terjadi Kesalahan"
            ];

            return response()->json($response, 500);
        }
    }

    public function changePassword(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'current_password' => 'required',
                'new_password' => 'required',
            ]);

            if ($validator->fails()) {
                $response = [
                    'status' => false,
                    'message' => $validator->errors()
                ];

                return response()->json($respons, 400);
            }

            $user = $request->user();
            if (!Hash::check($request->current_password, $user->password)) {
                $response = [
                    'status' => false,
                    'message' => "Kata sandi saat ini yang anda masukkan salah"
                ];

                return response()->json($respons, 400);
            }

            $user->forceFill([
                    'password' => Hash::make($request->new_password),
                ])->save();

            $response = [
                'success' => true,
                'message' => "Kata sandi berhasil diubah"
            ];

            return response()->json($response, 200);
        } catch (\Throwable $th) {
            $response = [
                'success' => false,
                'message' => "Terjadi Kesalahan"
            ];

            return response()->json($response, 500);
        }
    }

    public function destroy(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'password' => 'required',
            ]);

            if ($validator->fails()) {
                $response = [
                    'status' => false,
                    'message' => $validator->errors()
                ];

                return response()->json($respons, 400);
            }

            if (Hash::check($request->password, $request->user()->password)) {
                $user = User::findOrFail($request->user()->id);
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

                return response()->json($response, 400);
            }
        } catch (\Throwable $th) {
            $response = [
                'success' => false,
                'message' => "Terjadi Kesalahan"
            ];

            return response()->json($response, 500);
        }
    }
}
