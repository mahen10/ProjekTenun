<?php

namespace App\Http\Controllers\ApiController;

use App\Http\Controllers\Controller;
use App\Models\produk_tenun;
use Illuminate\Http\Request;

class produk_tenunController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        try {
            $produk = produk_tenun::latest()->get();
            return response()->json($produk, 201);
        } catch (\Exception $e) {
            return response()->json(
                [
                    'error' => 'failed to get data',
                    'massage' => $e->getMessage()
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
        $request->validate([
            'vendor_id' => 'required|exists:vendors,id',
            'nama_produk' => 'required|string',
            'kategori' => 'nullable|string',
            'stok' => 'required|integer',
            'harga_jual' => 'required|numeric',
            'gambar' => 'nullable|string|max:255', // atau validasi file kalau upload base64/file
        ]);

        $produk = produk_tenun::create($request->all());
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
        $produk->update($request->only([
            'nama_produk',
            'kategori',
            'stok',
            'harga_jual'
        ]));
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
