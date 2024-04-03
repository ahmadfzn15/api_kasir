<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Market;
use App\Models\Product;
use App\Models\Sale;
use App\Models\SaleDetail;
use App\Models\Category;
use App\Models\User;
use App\Models\Variant;
use DB;
use Validator;

class MarketController extends Controller
{
    public function index(Request $request)
    {
        try {
            $toko = $request->user()->with('market')->first();

            $response = [
                "status" => true,
                "message" => "Data Toko",
                "data" => $toko
            ];

            return response()->json($response, 200);
        } catch (\Throwable $th) {
            $response = [
                "status" => false,
                "message" => "Gagal mengambil data toko",
            ];

            return response()->json($response, 500);
        }
    }

    public function store(Request $request)
    {
        DB::beginTransaction();
        try {
            $validate = Validator::make($request->all(), [
                'nama_toko' => 'required|string',
                'alamat' => 'required|string',
                'no_tlp' => 'required|string',
                'bidang_usaha' => 'required|string'
            ]);

            if ($validate->fails()) {
                $response = [
                    'status' => false,
                    'message' => $validate->errors()
                ];

                return response()->json($respons, 500);
            }

            $market = Market::create($request->all());

            $request->user()->fill([
                "id_toko" => $market->id
            ]);
            $request->user()->save();

            $response = [
                'status' => true,
                'message' => 'Selamat Datang ' . $request->user()->nama
            ];

            DB::commit();
            return response()->json($response, 200);
        } catch (\Throwable $th) {
            DB::rollback();

            $response = [
                'status' => false,
                'message' => 'Toko baru gagal didaftarkan'
            ];

            return response()->json($response, 500);
        }
    }

    public function update(int $id, Request $request)
    {
        try {
            $validate = Validator::make($request->all(), [
                'nama_toko' => 'required|string',
                'alamat' => 'required|string',
                'no_tlp' => 'required|string',
                'bidang_usaha' => 'required|string'
            ]);

            if ($validate->fails()) {
                $response = [
                    'status' => false,
                    'message' => $validate->errors()
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
            $response = [
                'status' => false,
                'message' => 'Data toko gagal diubah'
            ];

            return response()->json($response, 500);
        }
    }

    public function destroy(int $id)
    {
        DB::beginTransaction();
        try {
            $Market = Market::findOrFail($id);
            $Market->delete();

            DB::commit();
            $response = [
                'status' => true,
                'message' => 'Toko berhasil dihapus'
            ];

            return response()->json($response, 200);
        } catch (\Throwable $th) {
            DB::rollback();

            $response = [
                'status' => false,
                'message' => 'Toko gagal dihapus'
            ];

            return response()->json($response, 500);
        }
    }

    public function clear(String $id)
    {
        DB::beginTransaction();
        try {
            SaleDetail::where('id_toko', $id)->truncate();
            Sale::where('id_toko', $id)->truncate();
            Product::where('id_toko', $id)->truncate();
            Category::where('id_toko', $id)->truncate();
            Variant::where('id_toko', $id)->truncate();
            User::where('id_toko', $id)->whereNot('role', 'admin')->truncate();

            DB::commit();
            $response = [
                'status' => true,
                'message' => 'Data berhasil direset'
            ];

            return response()->json($response, 200);
        } catch (\Throwable $th) {
            DB::rollback();

            $response = [
                'status' => false,
                'message' => 'Data gagal direset'
            ];

            return response()->json($response, 500);
        }
    }
}
