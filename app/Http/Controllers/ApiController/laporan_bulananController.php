<?php

namespace App\Http\Controllers\ApiController;

use App\Http\Controllers\Controller;
use App\Models\laporan_bulanan;
use Illuminate\Http\Request;

class laporan_bulananController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    { {
            try {
                $data = laporan_bulanan::latest()->get();
                return response()->json($data, 200);
            } catch (\Exception $e) {
                return response()->json(['error' => 'Gagal mengambil data', 'message' => $e->getMessage()], 500);
            }
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'vendor_id' => 'required|exists:tenuns,id',
            'bulan' => 'required|string',
            'total_penjualan' => 'required|numeric',
            'total_pembelian' => 'required|numeric',
        ]);

        $data = laporan_bulanan::create($request->all());
        return response()->json($data, 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        return laporan_bulanan::findOrFail($id);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $data = laporan_bulanan::findOrFail($id);

        $data->update($request->only([
            'bulan',
            'total_penjualan',
            'total_pembelian'
        ]));

        return response()->json($data);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        laporan_bulanan::destroy($id);
        return response()->json(['message' => 'Laporan berhasil dihapus']);
    }
}
