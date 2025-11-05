<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::create('produksi', function (Blueprint $table) {
            $table->id('id_produksi');
            $table->date('bulan');
            $table->bigInteger('jumlah_produksi')->unsigned();
            $table->timestamps();

            $table->unique(['bulan']);
        });
    }
    public function down()
    {
        Schema::dropIfExists('produksi');
    }
};
