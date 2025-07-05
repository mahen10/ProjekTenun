<?php

namespace App\Http\Controllers\ApiController;

use App\Http\Controllers\Controller;
use App\Models\produk_tenun;
use App\Models\Tenun;

use Illuminate\Http\Request;

class produk_tenunController extends Controller
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

            // Ambil produk hanya untuk vendor tersebut
            $produk = produk_tenun::where('vendor_id', $vendor->id)->latest()->get();
            return response()->json($produk, 200); // Status 200 untuk GET
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
        $user = auth()->user();
        if (!$user) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $vendor = Tenun::where('user_id', $user->id)->first();
        if (!$vendor) {
            return response()->json(['error' => 'Vendor not found'], 404);
        }

        $request->validate([
            'nama_produk' => 'required|string',
            'kategori' => 'nullable|string',
            'stok' => 'required|integer',
            'harga_jual' => 'required|numeric',
            'gambar' => 'nullable|image|mimes:jpeg,png,jpg|max:2048', // VALIDASI IMAGE
            'deskripsi' => 'nullable|string',
        ]);

        // Upload gambar (jika ada)
        $gambarPath = null;
        if ($request->hasFile('gambar')) {
            $gambarPath = $request->file('gambar')->store('produk_images', 'public');
        }

        $produk = produk_tenun::create([
            'vendor_id' => $vendor->id,
            'nama_produk' => $request->nama_produk,
            'kategori' => $request->kategori,
            'stok' => $request->stok,
            'harga_jual' => $request->harga_jual,
            'gambar' => $gambarPath,  // simpan path gambar
            'deskripsi' => $request->deskripsi,
        ]);
        return response()->json($produk, 201);
    }
    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        return produk_tenun::findOrFail($id);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $produk = produk_tenun::findOrFail($id);

        $request->validate([
            'nama_produk' => 'required|string',
            'kategori' => 'nullable|string',
            'stok' => 'required|integer',
            'harga_jual' => 'required|numeric',
            'gambar' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'deskripsi' => 'nullable|string',
        ]);

        // Upload gambar baru (jika ada)
        if ($request->hasFile('gambar')) {
            $gambarPath = $request->file('gambar')->store('produk_images', 'public');
            $produk->gambar = $gambarPath;
        }

        $produk->update([
            'nama_produk' => $request->nama_produk,
            'kategori' => $request->kategori,
            'stok' => $request->stok,
            'harga_jual' => $request->harga_jual,
            'deskripsi' => $request->deskripsi,
        ]);

        $produk->save();

        return response()->json($produk);
    }


    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        produk_tenun::destroy($id);
        return response()->json(['message' => 'Produk berhasil dihapus']);
    }
}
