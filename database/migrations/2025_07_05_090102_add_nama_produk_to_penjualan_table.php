<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
{
    Schema::table('penjualan', function (Blueprint $table) {
        $table->string('nama_produk')->nullable();
    });
}

public function down()
{
    Schema::table('penjualan', function (Blueprint $table) {
        $table->dropColumn('nama_produk');
    });
}
};
