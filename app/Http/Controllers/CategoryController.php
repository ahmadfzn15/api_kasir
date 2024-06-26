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
            $response = [
                "success" => false,
                "message" => "Terjadi Kesalahan"
            ];

            return response()->json($response, 500);
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

                return response()->json($respons, 400);
            }
            $user = $request->user();
            $category = Category::where('id_toko', $user->id_toko)->where('kategori', $request->kategori)->get();

            if ($category->count()) {
                $response = [
                    'status' => false,
                    'message' => 'Kategori sudah ada'
                ];

                return response()->json($response, 400);
            } else {
                Category::create([
                    "id_toko" => $user->id_toko,
                    "kategori" => $request->kategori
                ]);

                $response = [
                    'status' => true,
                    'message' => 'Kategori baru berhasil ditambahkan'
                ];

                return response()->json($response, 200);
            }
        } catch (\Throwable $th) {
            $response = [
                "success" => false,
                "message" => "Terjadi Kesalahan"
            ];

            return response()->json($response, 500);
        }
    }

    public function update(int $id, Request $request)
    {
        try {
            $validate = Validator::make($request->all(), [
                'kategori' => 'required',
            ]);

            if ($validate->fails()) {
                $response = [
                    'status' => false,
                    'message' => $validate->errors
                ];

                return response()->json($respons, 400);
            }

            Category::findOrFail($id)->update($request->all());

            $response = [
                'status' => true,
                'message' => 'Data kategori berhasil diubah'
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
                "success" => false,
                "message" => "Terjadi Kesalahan"
            ];

            return response()->json($response, 500);
        }
    }
}
