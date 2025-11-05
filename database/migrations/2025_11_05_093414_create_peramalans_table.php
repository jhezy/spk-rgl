<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::create('peramalan', function (Blueprint $table) {
            $table->id('id_peramalan');
            $table->date('periode_mulai');   // mulai data historis dipakai
            $table->date('periode_akhir');  // akhir data historis dipakai
            $table->date('forecast_bulan'); // bulan yang diprediksi (yyyy-mm-dd)
            $table->double('a', 16, 8);     // intercept
            $table->double('b1', 16, 8);    // koef penjualan (X1)
            $table->double('b2', 16, 8);    // koef permintaan (X2)
            $table->double('forecast_value', 16, 4)->nullable(); // Y prediksi
            $table->timestamps();
        });
    }
    public function down()
    {
        Schema::dropIfExists('peramalan');
    }
};
