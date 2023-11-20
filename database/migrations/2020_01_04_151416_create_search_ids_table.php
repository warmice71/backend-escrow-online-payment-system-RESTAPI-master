<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSearchIdsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('search_ids', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('search_string', 100);
            $table->unsignedBigInteger('item_id');
            $table->char('buyer_name', 100);
            $table->unsignedBigInteger('seller_id');
            $table->char('seller_email', 100);
            $table->timestamps();
            $table->foreign('item_id')->references('id')->on('items')->onDelete('cascade');
            $table->foreign('seller_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('search_ids');
    }
}
