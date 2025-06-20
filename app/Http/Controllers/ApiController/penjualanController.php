<?php

namespace App\Http\Controllers\ApiController;

use App\Models\penjualan;
use Illuminate\Http\Request;

use App\Http\Controllers\Controller;

class penjualanController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        try {
            $data = penjualan::latest()->get();
            return response()->json($data, 201);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'failed to get data',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'vendor_id' => 'required|exists:vendors,id',
            'produk_id' => 'required|exists:produk_tenun,id',
            'jumlah_terjual' => 'required|integer',
            'total_harga' => 'required|numeric',
            'tanggal_penjualan' => 'required|date',
        ]);

        $data = penjualan::create($request->all());
        return response()->json($data, 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        return Penjualan::findOrFail($id);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $data = penjualan::findOrFail($id);
        $data->update($request->only([
            'produk_id',
            'jumlah_terjual',
            'total_harga',
            'tanggal_penjualan'
        ]));
        return response()->json($data);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        penjualan::destroy($id);
        return response()->json(['message' => 'Data penjualan berhasil dihapus']);
    }
}
