<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Category;
use App\Models\User;
use Validator;

class CategoryController extends Controller
{
    public function index(Request $request)
    {
        try {
            $user = $request->user();
            $kategori = Category::where('id_toko', $user->id_toko)->get();

            $data = [
                "status" => true,
                "message" => "Data Kategori",
                "data" => $kategori
            ];

            return response()->json($data, 200);
        } catch (\Throwable $th) {
            return response()->json($th, 500);
        }
    }

    public function store(Request $request)
    {
        try {
            $validate = Validator::make($request->all(), [
                'kategori' => 'required',
            ]);

            if ($validate->fails()) {
                $response = [
                    'status' => false,
                    'message' => $validate->errors()
                ];

                return response()->json($respons, 500);
            }
            $user = $request->user();

            Category::create([
                "id_toko" => $user->id_toko,
                "kategori" => $request->kategori
            ]);

            $response = [
                'status' => true,
                'message' => 'Kategori baru berhasil ditambahkan'
            ];

            return response()->json($response, 200);
        } catch (\Throwable $th) {
            $response = [
                'status' => false,
                'message' => 'Kategori baru gagal ditambahkan'
            ];

            return response()->json($response, 500);
        }
    }

    public function update(int $id, Request $request)
    {
        try {
            // return response()->json(['message' => $request->all()], 500);
            $validate = Validator::make($request->all(), [
                'kategori' => 'required',
            ]);

            if ($validate->fails()) {
                $response = [
                    'status' => false,
                    'message' => $validate->errors
                ];

                return response()->json($respons, 500);
            }

            Category::findOrFail($id)->update($request->all());

            $response = [
                'status' => true,
                'message' => 'Data kategori berhasil diubah'
            ];

            return response()->json($response, 200);
        } catch (\Throwable $th) {
            $response = [
                'status' => false,
                'message' => 'Kategori gagal diubah'
            ];

            return response()->json($response, 500);
        }
    }

    public function destroy(int $id)
    {
        try {
            $Category = Category::findOrFail($id);
            $Category->delete();

            $response = [
                'status' => true,
                'message' => 'Data berhasil dihapus'
            ];

            return response()->json($response, 200);
        } catch (\Throwable $th) {
            $response = [
                'status' => true,
                'message' => 'Data gagal dihapus'
            ];

            return response()->json($response, 500);
        }
    }
}
