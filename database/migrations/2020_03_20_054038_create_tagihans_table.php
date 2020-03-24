<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTagihansTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tagihans', function (Blueprint $table) {
            $table->bigIncrements('tagihan_id');
            $table->unsignedBigInteger('pelanggan_id');
            $table->foreign('pelanggan_id')->references('pelanggan_id')->on('pelanggans')->onUpdate('cascade')->onDelete('cascade');
            $table->decimal('mtr_awal')->default(0);
            $table->decimal('mtr_akhir')->default(0);
            $table->decimal('mtr_jumlah')->default(0);
            $table->bigInteger('harga_m2')->default(0);
            $table->bigInteger('jml_m2')->default(0);
            $table->bigInteger('beban')->default(0);
            $table->bigInteger('total_tagihan')->default(0);
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('tagihans');
    }
}
