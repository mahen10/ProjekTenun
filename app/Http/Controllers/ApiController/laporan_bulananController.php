<?php

namespace App\Http\Controllers\ApiController;

use App\Http\Controllers\Controller;
use App\Models\laporan_bulanan;
use App\Models\pembelian;
use Illuminate\Http\Request;
use App\Models\penjualan;

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
                return response()->json([
                    'error' => 'Gagal mengambil data',
                    'message' => $e->getMessage()
                ], 500);
            }
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // Validasi input
        $request->validate([
            'vendor_id' => 'required|exists:vendors,id',
            'bulan' => 'required|regex:/^\d{4}-(0[1-9]|1[0-2])$/', // Contoh: 2025-06
        ]);

        // Ambil input
        $vendor_id = $request->vendor_id;
        $bulan = $request->bulan;

        // Pisahkan tahun dan bulan
        $tahun = substr($bulan, 0, 4); // ambil 4 digit pertama
        $angka_bulan = substr($bulan, 5, 2); // ambil 2 digit setelah tanda -

        // Hitung total penjualan
        $jumlah_terjual = penjualan::where('vendor_id', $vendor_id)
            ->whereYear('tanggal_penjualan', $tahun)
            ->whereMonth('tanggal_penjualan', $angka_bulan)
            ->sum('total_harga');

        // Hitung total pembelian
        $total_pembelian = pembelian::where('vendor_id', $vendor_id)
            ->whereYear('tanggal_pembelian', $tahun)
            ->whereMonth('tanggal_pembelian', $angka_bulan)
            ->sum('harga_total');

        // Simpan ke database
        $laporan = laporan_bulanan::create([
            'vendor_id' => $vendor_id,
            'bulan' => $bulan,
            'total_penjualan' => $jumlah_terjual,
            'total_pembelian' => $total_pembelian,
        ]);

        return response()->json($laporan, 201);
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
