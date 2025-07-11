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
        Schema::create('produk_tenun', function (Blueprint $table) {
            $table->id();
            $table->foreignId('vendor_id')->constrained('vendors')->onDelete('cascade');
            $table->string('nama_produk');
            $table->string('kategori')->nullable();
            $table->integer('stok')->default(0);
            $table->decimal('harga_jual', 12, 2);
            $table->text('deskripsi')->nullable()->after('gambar');
            $table->dropColumn('deskripsi');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('produk_tenuns');
    }
};
