<?php

namespace App\Http\Controllers\ApiController;

use App\Http\Controllers\Controller;
use App\Models\laporan_bulanan;
use App\Models\pembelian;
use Illuminate\Http\Request;
use App\Models\penjualan;
use App\Models\Tenun;



class laporan_bulananController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        try {
            $user = auth()->user();
            if (!$user) {
                return response()->json(['error' => 'Unauthorized'], 401);
            }

            $vendor = Tenun::where('user_id', $user->id)->first();
            if (!$vendor) {
                return response()->json(['error' => 'Vendor not found'], 404);
            }

            // Ambil bulan dari request atau default bulan sekarang
            $bulan = $request->bulan ?? date('Y-m');

            if (!preg_match('/^\d{4}-(0[1-9]|1[0-2])$/', $bulan)) {
                return response()->json(['error' => 'Format bulan tidak valid, gunakan YYYY-MM'], 422);
            }

            $tahun = substr($bulan, 0, 4);
            $angka_bulan = substr($bulan, 5, 2);

            // Hitung total penjualan
            $total_penjualan = penjualan::where('vendor_id', $vendor->id)
                ->whereYear('tanggal_penjualan', $tahun)
                ->whereMonth('tanggal_penjualan', $angka_bulan)
                ->sum('total_harga');
            // Ambil data penjualan per bulan untuk 1 tahun tertentu
            $laporan = laporan_bulanan::where('vendor_id', $vendor->id)
                ->whereYear('bulan', $tahun)
                ->orderBy('bulan')
                ->get(['bulan', 'total_penjualan']);

            // Hitung total pembelian
            $total_pembelian = pembelian::where('vendor_id', $vendor->id)
                ->whereYear('tanggal_pembelian', $tahun)
                ->whereMonth('tanggal_pembelian', $angka_bulan)
                ->sum('harga_total');

            return response()->json([
                'bulan' => $bulan,
                'total_penjualan' => $total_penjualan,
                'total_pembelian' => $total_pembelian,
                'grafik' => $laporan,
            ], 200);
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
        $request->validate([
            'bulan' => 'required|regex:/^\d{4}-(0[1-9]|1[0-2])$/', // Format: YYYY-MM
        ]);

        try {
            $user = auth()->user();
            if (!$user) {
                return response()->json(['error' => 'Unauthorized'], 401);
            }

            $vendor = Tenun::where('user_id', $user->id)->first();
            if (!$vendor) {
                return response()->json(['error' => 'Vendor not found'], 404);
            }

            $bulan = $request->bulan;
            $tahun = substr($bulan, 0, 4);
            $angka_bulan = substr($bulan, 5, 2);

            // Hitung total penjualan
            $total_penjualan = penjualan::where('vendor_id', $vendor->id)
                ->whereYear('tanggal_penjualan', $tahun)
                ->whereMonth('tanggal_penjualan', $angka_bulan)
                ->sum('total_harga');

            // Hitung total pembelian
            $total_pembelian = pembelian::where('vendor_id', $vendor->id)
                ->whereYear('tanggal_pembelian', $tahun)
                ->whereMonth('tanggal_pembelian', $angka_bulan)
                ->sum('harga_total');

            // Cek apakah laporan sudah ada
            $laporan = laporan_bulanan::updateOrCreate(
                [
                    'vendor_id' => $vendor->id,
                    'bulan' => $bulan,
                ],
                [
                    'total_penjualan' => $total_penjualan,
                    'total_pembelian' => $total_pembelian,
                ]
            );

            return response()->json($laporan, 200);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Gagal membuat laporan',
                'message' => $e->getMessage()
            ], 500);
        }
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
