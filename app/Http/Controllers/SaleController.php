<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Sale;
use App\Models\Product;
use App\Models\SaleDetail;
use App\Models\User;
use Illuminate\Support\Str;
use Validator;
use DB;

class SaleController extends Controller
{
    public function index(Request $request, Sale $sale, SaleDetail $saleDetail)
    {
        try {
            $user = $request->user();
            $history;
            if ($user->role == "admin") {
                $history = $sale->with('cashier')->where('id_toko', $user->id_toko)->latest()->get();
            } else {
                $history = $sale->where('id_toko', $user->id_toko)->where('id_kasir', $user->id)->latest()->get();
            }

            $data = [
                "success" => true,
                "message" => "Data histori",
                "data" => $history
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

    public function show(int $id, SaleDetail $saleDetail)
    {
        try {
            $sale = Sale::with('cashier')->findOrFail($id);
            $detail = $saleDetail->with('product')->where('id_penjualan', $id)->get();

            $data = [
                "success" => true,
                "message" => "Detail Histori",
                "data" => [
                    "sale" => $sale,
                    "detail" => $detail,
                ],
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

    public function store(Request $request, User $user, Sale $sale, SaleDetail $saleDetail, Product $product)
    {
        DB::beginTransaction();
        try {
            $validate = Validator::make($request->all(), [
                'cash' => 'nullable|string',
                'cashback' => 'nullable|integer',
                'total_harga' => 'required|integer',
                'status' => 'required|boolean',
                'total_pembayaran' => 'required|integer',
            ]);

            if ($validate->fails()) {
                $response = [
                    'status' => false,
                    'message' => $validate->errors()
                ];

                return response()->json($response, 400);
            }
            $user = $request->user();

            $kode = Str::random(10);
            $nomor_struk = $sale->where('kode', $kode)->get();

            while ($nomor_struk) {
                $kode = Str::random(10);
                $nomor_struk = $sale->where("kode", $kode)->first();
                if ($nomor_struk) {
                    continue;
                }
            }

            $sales = $sale->create([
                "kode" => $kode,
                "nama_pelanggan" => $request->nama_pelanggan,
                "id_toko" => $user->id_toko,
                "id_kasir" => $user->id,
                "cash" => $request->cash,
                "cashback" => $request->cashback,
                "total_harga" => $request->total_harga,
                "status" => $request->status,
                "biaya_tambahan" => $request->biaya_tambahan,
                "deskripsi_biaya_tambahan" => $request->deskripsi_biaya_tambahan,
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

                $p = $product->findOrFail($value['id']);
                if ($p->stok != null) {
                    $p->update([
                        "stok" => $p->stok - $value['qty']
                    ]);
                }
            }

            DB::commit();
            $response = [
                'success' => true,
                'data' => $sales,
                'message' => 'Pembayaran berhasil dilakukan',
            ];

            return response()->json($response, 200);
        } catch (\Throwable $th) {
            DB::rollback();
            $response = [
                "success" => false,
                "message" => "Terjadi Kesalahan"
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
                "success" => false,
                "message" => "Terjadi Kesalahan"
            ];

            return response()->json($response, 500);
        }
    }

    public function getSale(Request $request)
    {
        try {
            $id_toko = $request->user()->id_toko;
            $produk_terjual = SaleDetail::with('product')->whereRelation("product", "id_toko", $id_toko)->get();
            $transaksi_lunas = Sale::where("id_toko", $id_toko)->get();

            $produk_perhari = 0;
            $jumlah = 0;
            $omset = 0;
            $modal = 0;
            $produk = [];

            foreach ($produk_terjual->unique('id_produk') as $key => $value) {
                array_push($produk, ["namaProduk" => $value->product->namaProduk, "jumlah" => $produk_terjual->where('id_produk', $value->id_produk)->reduce(function ($prev, $next) {
                    return $prev + $next->qty;
                })]);
            }

            foreach ($produk_terjual as $i => $item) {
                $omset += $item->total_harga;
                $modal += $item->product->harga_beli * $item->qty;
                $jumlah += $item->qty;
                $produk_perhari += ($item->created_at->isToday()) ? $item->qty : 0;
            }

            $laba = $omset - $modal;

            $data = [
                "success" => true,
                "message" => "Data penjualan",
                "data" => [
                    "produk_terjual" => [
                        "jumlah" => $jumlah,
                        "data" => $produk,
                        "produk_perhari" => $produk_perhari
                    ],
                    "transaksi_lunas" => $transaksi_lunas->count(),
                    "modal" => $modal,
                    "omset" => $omset,
                    "laba" => $laba,
                ]
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
}
