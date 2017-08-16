<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateStockissueTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('stockissue', function (Blueprint $table) {
            $table->increments('id');
            $table->string('quantity', 50);
            $table->integer('salesmen_id')->unsigned();
            $table->integer('stock_id')->unsigned();
            $table->integer('solid')->default(0);
            $table->foreign('salesmen_id')->references('id')->on('salesmen')->onDelete('cascade');
            $table->foreign('stock_id')->references('id')->on('stock')->onDelete('cascade');
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('stockissue');
    }
}
