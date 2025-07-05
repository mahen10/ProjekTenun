<?php

namespace App\Http\Controllers\ApiController;

use App\Http\Controllers\Controller;
use App\Models\pembelian;
use Illuminate\Http\Request;

class pembelian_bahanController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        try {
            $user = auth()->user();
            if (!$user) {
                return response()->json(['error' => 'Unauthorized'], 401);
            }

            // Ambil vendor milik user yang login
            $vendor = \App\Models\Tenun::where('user_id', $user->id)->first();
            if (!$vendor) {
                return response()->json(['error' => 'Vendor not found'], 404);
            }

            // Ambil pembelian hanya milik vendor itu saja
            $pembelian = pembelian::where('vendor_id', $vendor->id)->latest()->get();

            return response()->json($pembelian, 200);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Gagal mengambil data',
                'message' => $e->getMessage()
            ], 500);
        }
    }


    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $user = auth()->user();
        if (!$user) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $vendor = \App\Models\Tenun::where('user_id', $user->id)->first();
        if (!$vendor) {
            return response()->json(['error' => 'Vendor not found'], 404);
        }

        $request->validate([
            'nama_bahan' => 'required|string',
            'jumlah' => 'required|integer',
            'harga_total' => 'required|numeric',
            'tanggal_pembelian' => 'required|date',
        ]);

        $pembelian = pembelian::create([
            'vendor_id' => $vendor->id,
            'nama_bahan' => $request->nama_bahan,
            'jumlah' => $request->jumlah,
            'harga_total' => $request->harga_total,
            'tanggal_pembelian' => $request->tanggal_pembelian,
        ]);

        return response()->json($pembelian, 201);
    }


    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        return pembelian::findOrFail($id);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $data = pembelian::findOrFail($id);

        $data->update($request->only([
            'nama_bahan',
            'jumlah',
            'harga_total',
            'tanggal_pembelian'
        ]));

        return response()->json($data);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        pembelian::destroy($id);
        return response()->json(['message' => 'Data pembelian berhasil dihapus']);
    }
}
