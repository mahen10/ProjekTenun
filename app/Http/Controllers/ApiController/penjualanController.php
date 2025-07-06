<?php

namespace App\Http\Controllers\ApiController;

use App\Models\produk_tenun; // Tambahkan import untuk model produk_tenun
use Illuminate\Support\Facades\DB; // Tambahkan import untuk transaksi
use App\Models\penjualan;
use App\Models\Tenun;
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
            $user = auth()->user();
            if (!$user) {
                return response()->json(['error' => 'Unauthorized'], 401);
            }

            // Cari vendor yang terkait dengan user
            $vendor = Tenun::where('user_id', $user->id)->first();
            if (!$vendor) {
                return response()->json(['error' => 'Vendor not found'], 404);
            }

            // Ambil penjualan hanya untuk vendor tersebut
            $data = penjualan::where('vendor_id', $vendor->id)->latest()->get();
            return response()->json($data, 200);
        } catch (\Exception $e) {
            return response()->json(
                [
                    'error' => 'Failed to get data',
                    'message' => $e->getMessage()
                ],
                500
            );
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try {
            $request->validate([
                'produk_id' => 'required|exists:produk_tenun,id',
                'jumlah_terjual' => 'required|integer|min:1',
                'tanggal_penjualan' => 'required|date',
            ]);

            $user = auth()->user();
            if (!$user) {
                return response()->json(['error' => 'Unauthorized'], 401);
            }

            $vendor = Tenun::where('user_id', $user->id)->first();
            if (!$vendor) {
                return response()->json(['error' => 'Vendor not found'], 404);
            }

            $produk = produk_tenun::findOrFail($request->produk_id);
            if ($produk->vendor_id != $vendor->id) {
                return response()->json(['error' => 'Unauthorized: Product does not belong to this vendor'], 403);
            }

            if ($produk->stok < $request->jumlah_terjual) {
                return response()->json(['error' => 'Insufficient stock'], 422);
            }

            $total_harga = $produk->harga_jual * $request->jumlah_terjual;

            DB::beginTransaction();

            $penjualan = penjualan::create([
                'vendor_id' => $vendor->id,
                'produk_id' => $request->produk_id,
                'nama_produk' => $produk->nama_produk, // ✅ ini wajib
                'jumlah_terjual' => $request->jumlah_terjual,
                'total_harga' => $total_harga,
                'tanggal_penjualan' => $request->tanggal_penjualan,
            ]);

            $produk->stok -= $request->jumlah_terjual;

            if ($produk->stok == 0) {
                $produk->delete();
            } else {
                $produk->save();
            }

            DB::commit();

            return response()->json($penjualan, 201);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json(['error' => 'Validation failed', 'messages' => $e->errors()], 422);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['error' => 'Failed to create sale', 'message' => $e->getMessage()], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        try {
            $penjualan = penjualan::findOrFail($id);
            return response()->json($penjualan, 200);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Sale not found',
                'message' => $e->getMessage(),
            ], 404);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        try {
            $request->validate([
                'produk_id' => 'required|exists:produk_tenun,id',
                'jumlah_terjual' => 'required|integer|min:1',
                'tanggal_penjualan' => 'required|date',
            ]);

            $penjualan = penjualan::findOrFail($id);
            $old_jumlah = $penjualan->jumlah_terjual;
            $produk = produk_tenun::findOrFail($request->produk_id);

            // Validasi bahwa produk milik vendor yang sama
            $user = auth()->user();
            if (!$user) {
                return response()->json(['error' => 'Unauthorized'], 401);
            }
            $vendor = Tenun::where('user_id', $user->id)->first();
            if (!$vendor || $produk->vendor_id != $vendor->id || $penjualan->vendor_id != $vendor->id) {
                return response()->json(['error' => 'Unauthorized: Invalid vendor'], 403);
            }

            // Validasi stok
            $stok_tersedia = $produk->stok + $old_jumlah; // Kembalikan stok lama
            if ($stok_tersedia < $request->jumlah_terjual) {
                return response()->json(['error' => 'Insufficient stock'], 422);
            }

            DB::beginTransaction();

            // Hitung total_harga baru
            $total_harga = $produk->harga_jual * $request->jumlah_terjual;

            // Update penjualan
            $penjualan->update([
                'produk_id' => $request->produk_id,
                'jumlah_terjual' => $request->jumlah_terjual,
                'total_harga' => $total_harga,
                'tanggal_penjualan' => $request->tanggal_penjualan,
            ]);

            // Update stok
            $produk->stok = $stok_tersedia - $request->jumlah_terjual;

            // Hapus produk jika stok menjadi 0
            if ($produk->stok == 0) {
                $produk->delete(); // Ini juga akan menghapus penjualan terkait karena onDelete('cascade')
            } else {
                $produk->save();
            }

            DB::commit();
            return response()->json($penjualan);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'error' => 'Validation failed',
                'messages' => $e->errors(),
            ], 422);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'error' => 'Failed to update sale',
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        try {
            $penjualan = penjualan::findOrFail($id);
            $produk = produk_tenun::find($penjualan->produk_id);

            DB::beginTransaction();

            // Kembalikan stok sebelum menghapus penjualan
            if ($produk) {
                $produk->stok += $penjualan->jumlah_terjual;
                $produk->save();
            }

            $penjualan->delete();

            DB::commit();
            return response()->json(['message' => 'Data penjualan berhasil dihapus']);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'error' => 'Failed to delete sale',
                'message' => $e->getMessage(),
            ], 500);
        }
    }
}
