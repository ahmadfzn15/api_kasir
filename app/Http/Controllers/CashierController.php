<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Validator;

class CashierController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();

        try {
            $data = User::where('role', 'kasir')->where('id_toko', $user->id_toko)->get();

            $response = [
                "success" => true,
                "data" => $data,
                "message" => "Data kasir"
            ];

            return response()->json($response, 200);
        } catch (\Throwable $th) {
            $response = [
                "success" => false,
                "data" => $data,
                "message" => "Gagal mengambil data karyawan"
            ];

            return response()->json($response, 500);
        }
    }

    public function store(Request $request)
    {
        try {
            $id_toko = $request->user()->id_toko;

            $validate = Validator::make($request->all(), [
                'username' => 'required|unique:users',
                'email' => 'nullable|email|unique:users',
                'password' => 'required'
            ]);

            if ($validate->fails()) {
                $response = [
                    'success' => false,
                    'message' => $validate->errors
                ];

                return response()->json($respons, 500);
            }

            User::create([
                "username" => $request->username,
                "email" => $request->email,
                "no_tlp" => $request->no_tlp,
                "password" => Hash::make($request->password),
                "role" => "kasir",
                "id_toko" => $id_toko,
            ]);

            $response = [
                'success' => true,
                'message' => 'Kasir baru berhasil ditambahkan'
            ];

            return response()->json($response, 200);
        } catch (\Throwable $th) {
            $response = [
                'success' => false,
                'message' => 'Kasir baru gagal ditambahkan'
            ];

            return response()->json($response, 500);
        }
    }
}
