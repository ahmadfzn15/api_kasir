<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Variant;
use Validator;

class VariantController extends Controller
{
    public function index()
    {
        try {
            $variant = Variant::all();

            $data = [
                "status" => true,
                "message" => "Data variant",
                "data" => $variant
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
                'variant' => 'required',
            ]);

            if ($validate->fails()) {
                $response = [
                    'status' => false,
                    'message' => $validate->errors
                ];

                return response()->json($respons, 200);
            }

            Variant::create($request->all());

            $response = [
                'status' => true,
                'message' => 'Data variant baru berhasil ditambahkan'
            ];

            return response()->json($response, 200);
        } catch (\Throwable $th) {
            return response()->json($th, 200);
        }
    }

    public function update(int $id, Request $request)
    {
        try {
            $validate = Validator::make($request->all(), [
                'variant' => 'required',
            ]);

            if ($validate->fails()) {
                $response = [
                'status' => false,
                'message' => $validate->errors
            ];

                return response()->json($respons, 200);
            }

            $variant = Variant::findOrFail($id);
            $variant->update($request->all());

            $response = [
                'status' => true,
                'message' => 'Data variant berhasil diubah'
            ];

            return response()->json($response, 200);
        } catch (\Throwable $th) {
            return response()->json($th, 500);
        }
    }

    public function destroy(int $id)
    {
        try {
            $variant = Variant::findOrFail($id);
            $variant->delete();

            $response = [
                'status' => true,
                'message' => 'Data berhasil dihapus'
            ];

            return response()->json($response, 200);
        } catch (\Throwable $th) {
            return response()->json($th, 500);
        }
    }
}
