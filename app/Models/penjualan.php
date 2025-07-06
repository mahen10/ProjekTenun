<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;

use Illuminate\Database\Eloquent\Model;

class penjualan extends Model
{
    use HasFactory;
    protected $table = 'penjualan'; // ✅ Tambahkan ini!
    protected $fillable = [
        'vendor_id',
        'produk_id',
        'nama_produk',
        'jumlah_terjual',
        'total_harga',
        'tanggal_penjualan',
    ];

    public function vendor()
    {
        return $this->belongsTo(Tenun::class, 'vendor_id');
    }

    public function produk()
    {
        return $this->belongsTo(produk_tenun::class, 'produk_id');
    }
}
