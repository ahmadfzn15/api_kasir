<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Validator;

class EmployeeController extends Controller
{
    public function index(Request $request)
    {

        try {
            $user = $request->user();
            $data = User::whereNot('role', 'admin')->where('id_toko', $user->id_toko)->get();

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
                "message" => "Gagal mengambil data kasir"
            ];

            return response()->json($response, 500);
        }
    }

    public function store(Request $request)
    {
        try {
            $id_toko = $request->user()->id_toko;

            $validate = Validator::make($request->all(), [
                'nama' => 'required|string',
                'email' => 'required|email|unique:users',
                'password' => 'required'
            ]);

            if ($validate->fails()) {
                $response = [
                    'success' => false,
                    'message' => $validate->errors()
                ];

                return response()->json($respons, 500);
            }

            User::create([
                "nama" => $request->nama,
                "email" => $request->email,
                "no_tlp" => $request->no_tlp,
                "password" => Hash::make($request->password),
                "role" => $request->role == 1 ? "staff kasir" : "staff inventaris",
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

    public function update(int $id, Request $request)
    {
        try {
            $id_toko = $request->user()->id_toko;

            $validate = Validator::make($request->all(), [
                'nama' => 'required|string',
                'email' => 'required|email',
            ]);

            if ($validate->fails()) {
                $response = [
                    'success' => false,
                    'message' => $validate->errors()
                ];

                return response()->json($respons, 500);
            }

            User::findOrFail($id)->update([
                "nama" => $request->nama,
                "email" => $request->email,
                "no_tlp" => $request->no_tlp,
                "role" => $request->role == 1 ? "staff kasir" : "staff inventaris",
            ]);

            $response = [
                'success' => true,
                'message' => 'Kasir baru berhasil diubah'
            ];

            return response()->json($response, 200);
        } catch (\Throwable $th) {
            $response = [
                'success' => false,
                'message' => 'Kasir baru gagal diubah'
            ];

            return response()->json($response, 500);
        }
    }
}
