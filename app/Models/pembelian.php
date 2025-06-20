<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class pembelian extends Model
{
    use HasFactory;
    protected $table = 'pembelian_bahan';
    protected $fillable = [
        'vendor_id',
        'nama_bahan',
        'jumlah',
        'harga_total',
        'tanggal_pembelian',
    ];

    public function vendor()
    {
        return $this->belongsTo(Tenun::class, 'vendor_id');
    }
}
