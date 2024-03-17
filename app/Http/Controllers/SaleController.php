<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Sale;
use App\Models\SaleDetail;
use App\Models\User;
use Validator;
use DB;

class SaleController extends Controller
{
    public function index(Request $request, Sale $sale)
    {
        try {
            $id_toko = $request->user()->id_toko;
            $histori = $sale->where('id_toko', $id_toko)->latest()->get();

            $data = [
                "success" => true,
                "message" => "Data histori",
                "data" => $histori
            ];

            return response()->json($data, 200);
        } catch (\Throwable $th) {
            $data = [
                "success" => false,
                "message" => "Gagal mengambil data histori",
            ];

            return response()->json($data, 500);
        }
    }

    public function show(int $id, DetailSale $detailSale)
    {
        try {
            $histori = $detailSale->where('id_penjualan', $id)->get();

            $data = [
                "success" => true,
                "message" => "Detail Histori",
                "data" => $histori
            ];

            return response()->json($data, 200);
        } catch (\Throwable $th) {
            $data = [
                "success" => false,
                "message" => "Gagal mengambil detail histori",
            ];

            return response()->json($data, 500);
        }
    }

    public function store(Request $request, User $user, Sale $sale, SaleDetail $saleDetail)
    {
        DB::beginTransaction();
        try {
            $validate = Validator::make($request->all(), [
                'cash' => 'required|integer',
                'cashback' => 'required|integer',
                'total_harga' => 'required|integer',
                'metode_pembayaran' => 'required|string',
                'total_pembayaran' => 'required|integer',
            ]);

            if ($validate->fails()) {
                $response = [
                    'status' => false,
                    'message' => $validate->errors
                ];

                return response()->json($respons, 500);
            }
            $user = $request->user();

            $sales = $sale->create([
                "id_toko" => $user->id_toko,
                "id_kasir" => $user->id,
                "cash" => $request->cash,
                "cashback" => $request->cashback,
                "total_harga" => $request->total_harga,
                "metode_pembayaran" => $request->metode_pembayaran,
                "diskon" => $request->diskon,
                "total_pembayaran" => $request->total_pembayaran,
                "ket" => $request->ket,
            ]);

            foreach ($request->order as $value) {
                $saleDetail->create([
                    "id_penjualan" => $sales->id,
                    "id_produk" => $value['id'],
                    "qty" => $value['qty'],
                    "total_harga" => $value['qty'] * $value['harga'],
                ]);
            }

            DB::commit();
            $response = [
                'success' => true,
                'message' => 'Pembayaran berhasil dilakukan',
            ];

            return response()->json($response, 200);
        } catch (\Throwable $th) {
            $response = [
                'success' => false,
                'message' => 'Pembayaran gagal'
            ];

            DB::rollback();
            return response()->json($response, 500);
        }
    }

    public function update(int $id, Request $request, Sale $sale)
    {
        try {
            $validate = Validator::make($request->all(), [
                'id_pelanggan' => 'nullable|numeric',
                'total_harga' => 'required|integer',
                'metode_pembayaran' => 'required',
                'status' => 'required',
                'diskon' => 'nullable|integer',
                'total_pembayaran' => 'required|integer',
            ]);

            if ($validate->fails()) {
                $response = [
                    'status' => false,
                    'message' => $validate->errors
                ];

                return response()->json($respons, 500);
            }

            $sale->findOrFail($id)->update($request->all());

            $response = [
                'status' => true,
                'message' => 'Data penjualan berhasil diubah'
            ];

            return response()->json($response, 200);
        } catch (\Throwable $th) {
            $response = [
                'status' => false,
                'message' => 'Data penjualan gagal diubah'
            ];

            return response()->json($response, 500);
        }
    }

    public function destroy(int $id, Sale $sale)
    {
        try {
            $Sale = $sale->findOrFail($id);
            $Sale->delete();

            $response = [
                'status' => true,
                'message' => 'Data penjualan berhasil dihapus'
            ];

            return response()->json($response, 200);
        } catch (\Throwable $th) {
            $response = [
                'status' => false,
                'message' => 'Data penjualan gagal dihapus'
            ];

            return response()->json($response, 500);
        }
    }
}
