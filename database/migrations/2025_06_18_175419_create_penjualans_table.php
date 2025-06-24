<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('penjualan', function (Blueprint $table) {
            // Hapus constraint lama
            $table->dropForeign(['produk_id']);
            // Tambahkan constraint baru dengan ON DELETE SET NULL
            $table->foreign('produk_id')
                  ->references('id')
                  ->on('produk_tenun')
                  ->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('penjualan', function (Blueprint $table) {
            // Kembalikan ke ON DELETE CASCADE jika rollback
            $table->dropForeign(['produk_id']);
            $table->foreign('produk_id')
                  ->references('id')
                  ->on('produk_tenun')
                  ->onDelete('cascade');
        });
    }
};
