<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRekeningsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('rekenings', function (Blueprint $table) {
            $table->bigIncrements('rekening_id');
            $table->unsignedBigInteger('pelanggan_id');
            $table->foreign('pelanggan_id')
                ->references('pelanggan_id')
                ->on('pelanggans')->onUpdate('cascade')
                ->onDelete('cascade');
            $table->bigInteger('hutang')->default(0);
            $table->bigInteger('simpanan')->default(0);
            $table->bigInteger('total_pembayaran')->default(0);
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
        Schema::dropIfExists('rekenings');
    }
}
