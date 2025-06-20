<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class laporan_bulanan extends Model
{
    use HasFactory;
    protected $table = 'laporan_bulanan';
    protected $fillable = [
        'vendor_id',
        'bulan',
        'total_penjualan',
        'total_pembelian',
    ];

    public function vendor()
    {
        return $this->belongsTo(Tenun::class, 'vendor_id');
    }
}
