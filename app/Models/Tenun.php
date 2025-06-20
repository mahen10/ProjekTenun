<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\laporan_bulanan;

class Tenun extends Model
{
    use HasApiTokens, HasFactory, Notifiable;
    protected $table = 'vendors'; // <--- TAMBAHKAN INI!
    protected $fillable = [
        'user_id',
        'nama_usaha',
        'alamat',
        'no_telepon',
    ];

    /**
     * Relasi: Vendor dimiliki oleh satu user.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Relasi: Vendor memiliki banyak produk tenun.
     */
    public function produkTenun()
    {
        return $this->hasMany(produk_tenun::class);
    }

    /**
     * Relasi: Vendor memiliki banyak penjualan.
     */
    public function penjualan()
    {
        return $this->hasMany(Penjualan::class);
    }

    /**
     * Relasi: Vendor memiliki banyak pembelian bahan.
     */
    public function pembelianBahan()
    {
        return $this->hasMany(pembelian::class);
    }

    /**
     * Relasi: Vendor memiliki banyak laporan bulanan.
     */
    public function laporan_bulanan()
    {
        return $this->hasMany(laporan_bulanan::class);
    }
}
