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
            $data = pembelian::latest()->get();
            return response()->json($data, 200);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Gagal mengambil data', 'message' => $e->getMessage()], 500);
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'vendor_id' => 'required|exists:tenuns,id',
            'nama_bahan' => 'required|string',
            'jumlah' => 'required|integer',
            'harga_total' => 'required|numeric',
            'tanggal_pembelian' => 'required|date',
        ]);

        $data = pembelian::create($request->all());
        return response()->json($data, 201);
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
