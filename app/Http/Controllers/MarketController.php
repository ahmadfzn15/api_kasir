<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Market;
use Validator;

class MarketController extends Controller
{
    public function store(Request $request)
    {
        try {
            $validate = Validator::make($request->all(), [
                'nama_toko' => 'required',
                'alamat' => 'required',
                'no_tlp' => 'required',
                'bidang_usaha' => 'required'
            ]);

            if ($validate->fails()) {
                $response = [
                    'status' => false,
                    'message' => $validate->errors
                ];

                return response()->json($respons, 200);
            }

            Market::create($request->all());

            $response = [
                'status' => true,
                'message' => 'Data toko baru berhasil dibuat'
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
                'nama_toko' => 'required',
                'alamat' => 'required',
                'no_tlp' => 'required',
                'bidang_usaha' => 'required'
            ]);

            if ($validate->fails()) {
                $response = [
                    'status' => false,
                    'message' => $validate->errors
                ];

                return response()->json($respons, 200);
            }

            Market::findOrFail($id)->update($request->all());

            $response = [
                'status' => true,
                'message' => 'Data toko berhasil diubah'
            ];

            return response()->json($response, 200);
        } catch (\Throwable $th) {
            return response()->json($th, 200);
        }
    }

    public function destroy(int $id)
    {
        try {
            $Market = Market::findOrFail($id);
            $Market->delete();

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
