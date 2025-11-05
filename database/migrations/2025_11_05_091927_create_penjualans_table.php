<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::create('penjualan', function (Blueprint $table) {
            $table->id('id_penjualan');
            $table->date('bulan'); // yyyy-mm-dd (pakai tanggal 1)
            $table->bigInteger('jumlah_penjualan')->unsigned();
            $table->timestamps();

            $table->unique(['bulan']);
        });
    }
    public function down()
    {
        Schema::dropIfExists('penjualan');
    }
};
