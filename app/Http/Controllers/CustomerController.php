<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Customer;
use Validator;

class CustomerController extends Controller
{
    public function index()
    {
        try {
            $pelanggan = Customer::all();

            $data = [
                "status" => true,
                "message" => "Data Pelanggan",
                "data" => $pelanggan
            ];

            return response()->json($data, 200);
        } catch (\Throwable $th) {
            return response()->json($th, 500);
        }
    }

    public function store(Request $request)
    {
        try {
            Customer::create($request->all());

            $response = [
                'status' => true,
                'message' => 'Data pelanggan baru berhasil ditambahkan'
            ];

            return response()->json($response, 200);
        } catch (\Throwable $th) {
            return response()->json($th, 500);
        }
    }

    public function update(int $id, Request $request)
    {
        try {
            $customer = Customer::findOrFail($id);
            $customer->update($request->all());

            $response = [
                'status' => true,
                'message' => 'Data pelanggan berhasil diubah'
            ];

            return response()->json($response, 200);
        } catch (\Throwable $th) {
            return response()->json($th, 500);
        }
    }

    public function destroy(int $id)
    {
        try {
            $Customer = Customer::findOrFail($id);
            $Customer->delete();

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
