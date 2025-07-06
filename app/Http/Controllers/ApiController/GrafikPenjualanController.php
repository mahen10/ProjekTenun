<?php

namespace App\Http\Controllers\ApiController;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\penjualan;
use App\Models\Tenun;

class GrafikPenjualanController extends Controller
{
    public function grafikTahunan(Request $request)
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

            $tahun = $request->tahun ?? date('Y');
            $data = [];

            for ($bulan = 1; $bulan <= 12; $bulan++) {
                $total_penjualan = penjualan::where('vendor_id', $vendor->id)
                    ->whereYear('tanggal_penjualan', $tahun)
                    ->whereMonth('tanggal_penjualan', $bulan)
                    ->sum('total_harga');

                $data[] = [
                    'bulan' => $bulan,
                    'total_penjualan' => $total_penjualan
                ];
            }

            return response()->json($data, 200);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Gagal ambil grafik', 'message' => $e->getMessage()], 500);
        }
    }
}
