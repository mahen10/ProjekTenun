<?php

namespace App\Http\Controllers\ApiController;

use App\Http\Controllers\Controller;
use App\Models\Tenun;
use Illuminate\Http\Request;

class TenunController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        try {
            $produk = Tenun::latest()->get();
            return response()->json($produk, 201);
        } catch (\Exception $e) {
            return response()->json(
                [
                    'error' => 'failed to create data',
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
        $validated = $request->validate([
            'user_id' => 'required|exists:users,id|unique:vendors,user_id',
            'nama_usaha' => 'required|string|max:255',
            'alamat' => 'nullable|string',
            'no_telepon' => 'nullable|string|max:20',
        ]);

        $vendor = Tenun::create($validated);
        return response()->json($vendor, 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        return Tenun::with('user')->findOrFail($id);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $vendor = Tenun::findOrFail($id);

        $validated = $request->validate([
            'nama_usaha' => 'sometimes|required|string|max:255',
            'alamat' => 'nullable|string',
            'no_telepon' => 'nullable|string|max:20',
        ]);

        $vendor->update($validated);
        return response()->json($vendor);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $vendor = Tenun::findOrFail($id);
        $vendor->delete();
        return response()->json(['message' => 'Vendor berhasil dihapus']);
    }
}
