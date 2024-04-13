<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\User;
use Validator;
use DB;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        try {
            $user = $request->user();
            $produk = [];
            if ($request->query('category') != 0) {
                $produk = Product::where('id_toko', $user->id_toko)->where('id_kategori', $request->query('category'))->latest()->get();
            } else {
                $produk = Product::where('id_toko', $user->id_toko)->latest()->get();
            }

            $response = [
                "status" => true,
                "message" => "Data Produk",
                "data" => $produk
            ];

            return response()->json($response, 200);
        } catch (\Throwable $th) {
            $response = [
                "status" => true,
                "message" => "Gagal mengambil data produk.",
            ];

            return response()->json($response, 500);
        }
    }

    public function show(int $id)
    {
        try {
            $produk = Product::findOrFail($id);

            $response = [
                "status" => true,
                "message" => "Detail Produk",
                "data" => $produk
            ];

            return response()->json($response, 200);
        } catch (\Throwable $th) {
            $response = [
                "status" => true,
                "message" => "Gagal mengambil detail produk produk",
            ];

            return response()->json($response, 500);
        }
    }

    public function store(Request $request)
    {
        try {
            $validate = Validator::make($request->all(), [
                'foto' => 'nullable|image|mimes:png,jpg,jpeg',
                'namaProduk' => 'required',
                'id_kategori' => 'required',
                'harga_beli' => 'required',
                'harga_jual' => 'required',
            ]);

            if ($validate->fails()) {
                $response = [
                    'status' => false,
                    'message' => $validate->errors()
                ];

                return response()->json($respons, 500);
            }

            $id = $request->user()->id_toko;
            $imageName = null;

            if ($request->hasFile('foto')) {
                $image = $request->file('foto');
                $imageName = time().'.'.$image->getClientOriginalExtension();
                $image->storeAs('public/img', $imageName);
            }

            Product::create([
                "foto" => $imageName,
                "namaProduk" => $request->namaProduk,
                "barcode" => $request->barcode,
                "id_kategori" => $request->id_kategori,
                "id_toko" => $id,
                "harga_beli" => $request->harga_beli,
                "harga_jual" => $request->harga_jual,
                "deskripsi" => $request->deskripsi,
                "stok" => $request->stok,
            ]);

            $response = [
                'status' => true,
                'message' => 'Produk baru berhasil ditambahkan'
            ];

            return response()->json($response, 200);
        } catch (\Throwable $th) {
            $response = [
                'status' => true,
                'message' => 'Produk baru gagal ditambahkan'
            ];

            return response()->json($response, 500);
        }
    }

    public function update(int $id, Request $request)
    {
        try {
            $validate = Validator::make($request->all(), [
                'old_img' => 'nullable|string',
                'new_img' => 'nullable|image',
                'namaProduk' => 'required|string',
                'barcode' => 'nullable|string',
                'id_kategori' => 'required',
                'harga_beli' => 'required',
                'harga_jual' => 'required',
                'deskripsi' => 'nullable|string',
            ]);

            if ($validate->fails()) {
                $response = [
                    'status' => false,
                    'message' => $validate->errors()
                ];

                return response()->json($respons, 500);
            }


            $product = Product::findOrFail($id);
            $new_img = null;
            if ($request->hasFile('new_img')) {
                if ($product->foto) {
                    Storage::delete("img/" . $product->foto);
                }
                $image = $request->file('new_img');
                $new_img = time().'.'.$image->getClientOriginalExtension();
                $image->storeAs('public/img', $new_img);
            } else if ($product->foto && !$request->old_img) {
                Storage::delete("img/" . $product->foto);
            }

            $product->update([
                "foto" => $new_img ?? $request->old_img,
                "namaProduk" => $request->namaProduk,
                "barcode" => $request->barcode,
                "id_kategori" => $request->id_kategori,
                "harga_beli" => $request->harga_beli,
                "harga_jual" => $request->harga_jual,
                "deskripsi" => $request->deskripsi,
                "stok" => $request->stok
            ]);

            $response = [
                'status' => true,
                'message' => 'Data produk berhasil diubah'
            ];

            return response()->json($response, 200);
        } catch (\Throwable $th) {

            $response = [
                'status' => false,
                'message' => "Data produk gagal diubah"
            ];

            return response()->json($response, 500);
        }
    }

    public function destroy(Request $request)
    {
        try {
            DB::beginTransaction();
            foreach ($request->data as $i) {
                $product = Product::findOrFail($i);
                $product->delete();
            }

            $response = [
                'status' => true,
                'message' => 'Data berhasil dihapus'
            ];

            DB::commit();
            return response()->json($response, 200);
        } catch (\Throwable $th) {
            DB::rollback();

            $response = [
                'status' => true,
                'message' => 'Data gagal dihapus'
            ];

            return response()->json($response, 500);
        }
    }
}
