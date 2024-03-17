<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Kelas;
use Illuminate\Http\Request;
use Validator;

class ApiController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        try {
            $user = User::all();
            $data = [
                "status" => true,
                "message" => "Data User",
                "data" => $user
            ];

            return response()->json($data, 200);
        } catch (\Throwable $th) {
            return response()->json(["message" => "Failed to fetching data."], 500);
        }
    }

    public function kelas()
    {
        try {
            $kelas = Kelas::all();

            $data = [
                "status" => true,
                "message" => "Data Kelas",
                "data" => $kelas
            ];

            return response()->json($data, 200);
        } catch (\Throwable $th) {
            return response()->json(["message" => "Failed to fetching data."], 500);
        }
    }

    public function editKelas($id, Request $request) {
        Kelas::where("id", $id)->update([
            "kelas" => $request->kelas
        ]);

        return response()->json("Data berhasil diedit.", 200);
    }

    public function storeKelas(Request $request) {
        $validate = Validator::make($request->all(), [
            'kelas' => 'required'
        ])->setAttributeNames([
            'kelas' => 'Inputan kelas'
        ]);

        if ($validate->fails()) {
            $response = [
                'status' => false,
                'message' => $validate->errors
            ];

            return response()->json($respons, 200);
        }
        $db = new Kelas();
        $db->kelas = $request->kelas;
        $db->save();

        $response = [
            'status' => true,
            'message' => 'kelas successfully created'
        ];

        return response()->json($response, 200);
    }

}
