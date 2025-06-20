<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class produk_tenun extends Model
{
    // Nama tabel (opsional jika sudah sesuai dengan konvensi)
    protected $table = 'produk_tenun';

    // Kolom yang bisa diisi massal (fillable)
    protected $fillable = [
        'vendor_id',
        'nama_produk',
        'kategori',
        'stok',
        'harga_jual',
        'gambar',
    ];

    // Relasi: ProdukTenun milik satu vendor
    public function vendor()
    {
        return $this->belongsTo(Tenun::class);
    }

    // (Opsional) Jika ingin menambahkan format harga
    public function getFormattedHargaAttribute()
    {
        return 'Rp ' . number_format($this->harga_jual, 0, ',', '.');
    }
}
