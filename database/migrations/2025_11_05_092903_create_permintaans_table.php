<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::create('permintaan', function (Blueprint $table) {
            $table->id('id_permintaan');
            $table->date('bulan');
            $table->bigInteger('jumlah_permintaan')->unsigned();
            $table->timestamps();

            $table->unique(['bulan']);
        });
    }
    public function down()
    {
        Schema::dropIfExists('permintaan');
    }
};
