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
                "message" => "Gagal mengambil data user"
            ];

            return response()->json($response, 500);
        }
    }

    public function update(Request $request)
    {
        try {
            return response()->json(['message' => $request->all()], 500);
            $validated = Validator::make($request, [
                'nama' => 'required|string|max:255',
                'email' => 'required|email',
            ]);

            if ($validated->fails()) {
                $response = [
                    'success' => false,
                    'message' => $validated->errors(),
                    'request' => $request->all()
                ];

                return response()->json($response, 500);
            }

            $user = $request->user();
            $imageName = null;
            if ($request->hasFile('foto')) {
                if ($user->foto) {
                    Storage::delete("img/" . $user->foto);
                }
                $image = $request->file('foto');
                $imageName = time().'.'.$image->getClientOriginalExtension();
                $image->storeAs('public/img', $imageName);
            }

            $user->fill([
                'foto' => $imageName,
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
                "message" => "Profil gagal diubah"
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

                return response()->json($respons, 500);
            }

            $user = $request->user();
            if (!Hash::check($request->current_password, $user->password)) {
                $response = [
                    'status' => false,
                    'message' => "Kata sandi saat ini yang anda masukkan salah"
                ];

                return response()->json($respons, 500);
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
                'message' => "Kata sandi gagal diubah"
            ];

            return response()->json($response, 500);
        }
    }

    public function destroy(int $id, Request $request)
    {
        try {
            $user = User::findOrFail($id);
            $user->delete();

            $response = [
                'success' => true,
                'message' => "Akun berhasil dihapus"
            ];

            return response()->json($response, 200);
        } catch (\Throwable $th) {
            $response = [
                'success' => false,
                'message' => "Akun gagal dihapus"
            ];

            return response()->json($response, 500);
        }
    }
}
