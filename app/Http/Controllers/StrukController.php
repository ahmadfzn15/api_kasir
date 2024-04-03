<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Sale;
use App\Models\SaleDetail;

class StrukController extends Controller
{
    public function index(Request $request)
    {
        try {
            $id_toko = $request->user()->id_toko;
            $struk = Sale::with(['market', 'cashier'])->get();

            $data = [
                "success" => true,
                "message" => "Data Struk",
                "data" => $struk
            ];

            return response()->json($data, 200);
        } catch (\Throwable $th) {
            $data = [
                "success" => false,
                "message" => "Gagal mengambil data struk",
            ];

            return response()->json($data, 500);
        }
    }
}
