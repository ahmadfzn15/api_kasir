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
                "success" => false,
                "message" => "Terjadi Kesalahan"
            ];

            return response()->json($response, 500);
        }
    }

    public function store(Request $request)
    {
        DB::beginTransaction();
        try {
            $validate = Validator::make($request->all(), [
                'logo' => 'nullable|image|mimes:png,jpg,jpeg',
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

                return response()->json($respons, 400);
            }

            $id = $request->user()->id_toko;
            $imageName = null;

            if ($request->hasFile('logo')) {
                $image = $request->file('logo');
                $imageName = time().'.'.$image->getClientOriginalExtension();
                $image->storeAs('public/img', $imageName);
            }

            $market = Market::create([
                "logo" => $imageName,
                "nama_toko" => $request->nama_toko,
                "alamat" => $request->alamat,
                "no_tlp" => $request->no_tlp,
                "bidang_usaha" => $request->bidang_usaha,
            ]);

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
                'old_img' => 'nullable|string',
                'new_img' => 'nullable|image',
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

                return response()->json($respons, 400);
            }

            $product = Market::findOrFail($id);
            $new_img = null;
            if ($request->hasFile('new_img')) {
                if ($product->logo) {
                    Storage::delete("img/" . $product->logo);
                }
                $image = $request->file('new_img');
                $new_img = time().'.'.$image->getClientOriginalExtension();
                $image->storeAs('public/img', $new_img);
            } else if ($product->logo && !$request->old_img) {
                Storage::delete("img/" . $product->logo);
            }

            Market::findOrFail($id)->update([
                "logo" => $new_img ?? $request->old_img,
                "nama_toko" => $request->nama_toko,
                "alamat" => $request->alamat,
                "no_tlp" => $request->no_tlp,
                "bidang_usaha" => $request->bidang_usaha,
            ]);

            $response = [
                'status' => true,
                'message' => 'Data toko berhasil diubah'
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
                "success" => false,
                "message" => "Terjadi Kesalahan"
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
                "success" => false,
                "message" => "Terjadi Kesalahan"
            ];

            return response()->json($response, 500);
        }
    }
}
